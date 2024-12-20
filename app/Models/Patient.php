<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'cin',
        'date',
        'address',
        'sex',
        'phone_number',
        'mutuelle',
        'note',
        'allergy',
        'disease',
        'referral'
    ];
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }
    public function Ordonance()
    {
        return $this->hasMany(Ordonance::class, 'patient_id');
    }
    public function Xray()
    {
        return $this->hasMany(Xray::class, 'patient_id');
    }
    protected static function boot()
    {
        parent::boot();
        static::created(function ($patient) {
            // Use the patient's ID to create a folder
            $patientFolder = 'patients/' . $patient->id;
            // Create the folder using the Storage facade
            Storage::disk('public')->makeDirectory($patientFolder);
            // Assign the folder to the patient
            $patient->p_folder = $patientFolder;
            $patient->save(); // Save the patient to persist the changes
        });
    }
    public function waitingRoom()
    {
        return $this->hasOne(WaitingRoom::class);
    }
}
