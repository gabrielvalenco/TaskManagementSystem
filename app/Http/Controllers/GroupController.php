<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Auth::user()->groups;
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'members' => 'array|min:1|max:3',
            'members.*' => 'email|exists:users,email'
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'created_by' => Auth::id(),
        ]);

        // Add creator as admin
        $group->members()->attach(Auth::id(), ['role' => 'admin']);

        // Add other members
        if (!empty($validated['members'])) {
            $members = User::whereIn('email', $validated['members'])->pluck('id');
            $group->members()->attach($members, ['role' => 'member']);
        }

        return redirect()->route('groups.show', $group)
            ->with('success', 'Grupo criado com sucesso!');
    }

    public function show(Group $group)
    {
        if (!$group->isMember(Auth::user())) {
            abort(403);
        }

        $tasks = $group->tasks()->with(['category', 'creator'])->get();
        $members = $group->members()->get();

        return view('groups.show', compact('group', 'tasks', 'members'));
    }

    public function addMember(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::user())) {
            abort(403);
        }

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($group->members()->count() >= 4) {
            return back()->with('error', 'O grupo já atingiu o limite máximo de 4 membros.');
        }

        if (!$group->isMember($user)) {
            $group->members()->attach($user->id, ['role' => 'member']);
            return back()->with('success', 'Membro adicionado com sucesso!');
        }

        return back()->with('error', 'Este usuário já é membro do grupo.');
    }

    public function removeMember(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::user())) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validated['user_id'] == Auth::id()) {
            return back()->with('error', 'Você não pode remover a si mesmo do grupo.');
        }

        $group->members()->detach($validated['user_id']);
        return back()->with('success', 'Membro removido com sucesso!');
    }

    public function delete(Group $group)
    {
        $group->delete();
        return redirect()->route('groups.index')->with('success', 'Grupo deletado com sucesso!');
    }

    public function destroy(Group $group)
    {
        $group->delete();
        return redirect()->route('groups.index')
            ->with('success', 'Grupo removido com sucesso!');
    }

    public function leave(Group $group)
    {
        $group->members()->detach(Auth::id());
        return redirect()->route('groups.index')
            ->with('success', 'Saída realizada com sucesso!');
    }
}
