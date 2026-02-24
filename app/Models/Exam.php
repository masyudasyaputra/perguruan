<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Exam extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment).
     * Sesuai dengan input dari form tambah dan edit jadwal.
     */
    protected $fillable = [
        'name',
        'execution_date',
        'location',
        'province_id',
        'status'
    ];

    /**
     * Casting tipe data kolom.
     * Menggunakan 'datetime' sangat penting agar field execution_date 
     * otomatis menjadi objek Carbon, sehingga bisa menggunakan fungsi ->format().
     */
    protected $casts = [
        'execution_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke tabel ExamParticipant.
     * Satu sesi ujian memiliki banyak peserta yang terdaftar.
     */
    public function participants()
    {
        return $this->hasMany(ExamParticipant::class, 'exam_id');
    }

    /**
     * Relasi ke tabel Province.
     * Menghubungkan ujian dengan wilayah provinsi penyelenggara.
     */
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    /**
     * Accessor (Opsional): Untuk mempermudah pengecekan status secara konsisten.
     * Contoh penggunaan: $exam->is_open
     */
    public function getIsOpenAttribute()
    {
        return $this->status === 'open';
    }

    /**
     * Helper: Menghitung jumlah peserta yang sudah lulus di sesi ini.
     */
    public function getPassedParticipantsCountAttribute()
    {
        return $this->participants()->where('result', 'pass')->count();
    }

    public function examiners()
    {
        return $this->belongsToMany(\App\Models\User::class, 'exam_examiners', 'exam_id', 'user_id')
            ->withTimestamps();
    }
}