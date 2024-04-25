<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Subject extends Model
{
    //
    protected $fillable=['name','short_code','maneb_code','exam_fee','school_id'];

    public $timestamps = false;

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function ScopeSchool($query)
    {
        return $query->whereSchoolId(Auth::user()->school_id);
    }

    public function ScopeTerm($query,$term)
    {
        return $query->whereTermId($term);
    }

    public function ScopeSubject($query)
    {
        return $query->select('name','id');
    }
    public function ScopeCodes($query)
    {
        return $query->select('short_code','id');
    }

    public function schools()
    {
        return $this->belongsToMany(School::class);
    }
    public function grade()
    {
        return $this->hasMany(Grades::class);
    }
    public function grades()
    {
        return $this->hasOne(Grades::class,'subject_id','id');
    }
    public function notes()
    {
        return $this->hasMany(Note::class,'category','name')->where('origin','App\Notes');
    }
    public function assignments()
    {
        return $this->hasMany(Note::class,'category','name')->where('origin','App\Assignment');
    }
    public function tests()
    {
        return $this->hasMany(QuestionMeta::class);
    }
    public function timetable()
    {
        return $this->hasMany(Timetable::class);
    }

}
