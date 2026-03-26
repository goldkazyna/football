<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        return view('profile.show', compact('user'));
    }

    public function edit(Request $request)
    {
        $user = $request->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
        ], [
            'name.required' => 'Введите имя.',
            'avatar.image' => 'Файл должен быть изображением.',
            'avatar.max' => 'Максимальный размер аватара — 2 МБ.',
        ]);

        $user = $request->user();
        $user->name = $request->name;
        $user->city = $request->city;
        $user->specialization = $request->specialization;

        if ($request->hasFile('avatar')) {
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return redirect()->route('profile.show')->with('success', 'Профиль обновлён.');
    }

    public function reuploadDocument(Request $request)
    {
        $request->validate([
            'doc_diploma' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'doc_id' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'doc_pension' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ], [
            'doc_diploma.required' => 'Загрузите копию диплома.',
            'doc_diploma.mimes' => 'Допустимые форматы: JPG, PNG, PDF.',
            'doc_diploma.max' => 'Максимальный размер — 10 МБ.',
            'doc_id.required' => 'Загрузите удостоверение личности.',
            'doc_id.mimes' => 'Допустимые форматы: JPG, PNG, PDF.',
            'doc_id.max' => 'Максимальный размер — 10 МБ.',
            'doc_pension.required' => 'Загрузите выписку из пенсионного фонда.',
            'doc_pension.mimes' => 'Допустимые форматы: JPG, PNG, PDF.',
            'doc_pension.max' => 'Максимальный размер — 10 МБ.',
        ]);

        $user = $request->user();

        $user->update([
            'doc_diploma' => $request->file('doc_diploma')->store('verification_documents', 'public'),
            'doc_id' => $request->file('doc_id')->store('verification_documents', 'public'),
            'doc_pension' => $request->file('doc_pension')->store('verification_documents', 'public'),
            'verification_status' => 'pending',
        ]);

        return redirect()->route('dashboard')->with('success', 'Документы отправлены на повторную проверку.');
    }

    public function payments(Request $request)
    {
        $payments = $request->user()->payments()->latest()->paginate(20);
        return view('profile.payments', compact('payments'));
    }
}
