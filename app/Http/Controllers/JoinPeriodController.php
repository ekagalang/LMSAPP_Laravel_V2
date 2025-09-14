<?php

namespace App\Http\Controllers;

use App\Models\CoursePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JoinPeriodController extends Controller
{
    public function showJoinForm()
    {
        return view('period.join');
    }

    public function join(Request $request)
    {
        $request->validate([
            'token' => 'required|string|max:50',
        ], [
            'token.required' => 'Token bergabung wajib diisi',
            'token.string' => 'Token harus berupa teks',
            'token.max' => 'Token terlalu panjang',
        ]);

        $token = strtoupper(trim($request->token));
        $period = CoursePeriod::findByToken($token);

        if (!$period) {
            return back()->withInput()->withErrors([
                'token' => 'Token tidak valid atau tidak ditemukan'
            ]);
        }

        $userId = Auth::id();

        // Check if user can join with token
        if (!$period->canJoinWithToken($userId)) {
            $errors = [];

            if ($period->isCompleted()) {
                $errors['token'] = 'Periode sudah selesai, tidak bisa bergabung lagi';
            } elseif (!$period->hasAvailableSlots()) {
                $errors['token'] = 'Periode sudah penuh, tidak ada slot tersedia';
            } elseif ($period->isParticipant($userId)) {
                $errors['token'] = 'Anda sudah terdaftar dalam periode ini';
            } else {
                $errors['token'] = 'Tidak dapat bergabung ke periode ini';
            }

            return back()->withInput()->withErrors($errors);
        }

        // Join the period
        $period->participants()->attach($userId, [
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('courses.show', $period->course)
            ->with('success', "Berhasil bergabung ke periode '{$period->name}' untuk kursus '{$period->course->title}'!");
    }

    public function joinByToken($token)
    {
        $token = strtoupper(trim($token));
        $period = CoursePeriod::findByToken($token);

        if (!$period) {
            return redirect()->route('join.form')->withErrors([
                'token' => 'Token tidak valid atau tidak ditemukan'
            ])->withInput(['token' => $token]);
        }

        return view('period.join-confirm', compact('period', 'token'));
    }

    public function confirmJoin(Request $request, $token)
    {
        $token = strtoupper(trim($token));
        $period = CoursePeriod::findByToken($token);

        if (!$period) {
            return redirect()->route('join.form')->withErrors([
                'token' => 'Token tidak valid atau tidak ditemukan'
            ]);
        }

        $userId = Auth::id();

        // Check if user can join with token
        if (!$period->canJoinWithToken($userId)) {
            $errors = [];

            if ($period->isCompleted()) {
                $errors['general'] = 'Periode sudah selesai, tidak bisa bergabung lagi';
            } elseif (!$period->hasAvailableSlots()) {
                $errors['general'] = 'Periode sudah penuh, tidak ada slot tersedia';
            } elseif ($period->isParticipant($userId)) {
                $errors['general'] = 'Anda sudah terdaftar dalam periode ini';
            } else {
                $errors['general'] = 'Tidak dapat bergabung ke periode ini';
            }

            return back()->withErrors($errors);
        }

        // Join the period
        $period->participants()->attach($userId, [
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('courses.show', $period->course)
            ->with('success', "Selamat! Anda berhasil bergabung ke periode '{$period->name}' untuk kursus '{$period->course->title}'!");
    }
}