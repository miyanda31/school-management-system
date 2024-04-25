<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

//use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
        protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setNewApiToken()
    {
        $this->api_token = Str::uuid();
        $this->save();
    }
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function guardian()
    {
        return $this->belongsToMany(static::class,'wards','ward_id','user_id');
    }


    public function ward()
    {
        return $this->belongsToMany(static::class,'wards','user_id','ward_id');
    }

    public function ScopeSchoolId($query)
    {
        return $query->whereSchoolId(Auth::user()->school_id);
    }
    public function ScopeStatus($query, $status)
    {
        return $query->whereStatus($status);
    }
    public function ScopeActive($query)
    {
        return $query->status(1);
    }
    public function ScopeInActive($query)
    {
	    return $query->status(0);
    }
    public function scopeName($query)
    {
        return $query->select(DB::raw('concat(left(fname,1),". ",lname) as name'),'id');
    }
    public function scopeFullname($query)
    {
        return $query->select(DB::raw('concat(lname," ",fname) as name'),'id');
    }
    public function scopeSimple($query)
    {
        return $query->select('id',DB::raw('concat(lname," ",fname) as name'),'username',DB::raw('gender'),'student_id');
    }
    public function ScopeMale($query)
    {
        return $query->whereGender('male');
    }
    public function ScopeFemale($query)
    {
        return $query->whereGender('female');
    }
    public function ScopeAdmin($query)
    {
        return $query->whereType('Admin');
    }
    public function ScopeType($query,$type)
    {
        return $query->whereType($type);
    }
    public function ScopeSearch($query,$name)
    {
        return $query->where(function ($q) use ($name) {
        $q->where('fname','like','%'.$name.'%')
            ->orWhere('lname','like','%'.$name.'%');
    });
    }

    public function ScopeParentOrTeacher($query)
    {
        return $query->where(function ($q)  {
        $q->where('type','parent')
            ->orWhere('type','teacher');
    });
    }
    public function ScopeStudentOrTeacher($query)
    {
        return $query->where(function ($q)  {
        $q->where('type','Student')
            ->orWhere('type','teacher');
    });
    }
    public function ScopeAdminOrTeacher($query)
    {
        return $query->where(function ($q)  {
        $q->where('type','Admin')
            ->orWhere('type','Teacher');
        });
    }


    public function ScopeParent($query)
    {
        return $query->whereType('parent');
    }
    public function ScopeSelectable($query)
    {
        return $query->select('role_id',DB::raw('concat(fname," ",lname) as name'),'gender','id','avatar','fname','lname','phone');
    }
    public function ScopeProfile($query)
    {
        return $query->select(DB::raw('concat(lname," ",fname) as name'),'avatar','type','username','id','gender','fname','lname');
    }

    public function ScopeInitials($query)
    {
        return $query->select('id',DB::raw('concat(left(fname,1),". ",lname) as name'),DB::raw('left(gender,1) as gender'),'avatar','fname','username');
    }

    public function ScopeTeacher($query)
    {
        return $query->whereType('Teacher');
    }

    public function ScopeStudent($query)
    {
        return $query->whereType('Student');
    }

    public function code()
    {
        return $this->hasOne(Code::class);
    }



    public function cause()
    {
        return $this->hasMany(Cause::class);
    }

    public function timetable()
    {
        return $this->hasMany(Timetable::class);
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function post()
    {
        return $this->hasMany(Post::class);
    }


    public function biography()
    {
        return $this->hasMany(Biography::class);
    }

    public function profiles()
    {
        return $this->hasOne(Profile::class);
    }

    public function subject()
    {
        return $this->belongsToMany(Subject::class);
    }
    public function club()
    {
        return $this->belongsToMany(Club::class);
    }

    public function group()
    {
        return $this->belongsToMany(Group::class);
    }

    public function dropout()
    {
        return $this->hasOne(Dropout::class);
    }

    public function issue()
    {
        return $this->hasMany(Issue::class);
    }

    public function allocation()
    {
        return $this->hasMany(Allocation::class);
    }

    public function form()
    {
        return $this->belongsToMany(Form::class)->withPivot('calendar_id')->wherePivot('calendar_id',$this->calendar()->id);
        //
    }

    public function calendar()
    {
       $calendar = Calendar::whereHas('term',function ($q){
            $q->status();
        })->first();
        return $calendar;
    }

    public function teachers()
    {
        return $this->hasOne(Teacher::class);
    }

    public function department()
    {
        return $this->belongsToMany(Department::class);
    }

    public function scheme()
    {
        return $this->hasMany(Scheme::class);
    }
    public function grade()
    {
        return $this->hasOne(Grade::class);
    }
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function score()
    {
        return $this->hasOne(Score::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }

    public function recently()
    {
        return $this->hasOne(Payment::class)->latest();
    }

    public function maneb()
    {
        return $this->hasMany(Maneb::class);
    }
    public function category()
    {
        return $this->hasOne(Maneb::class,'user_id');
    }
    public function terminated()
    {
        return $this->hasOne(Terminate::class);
    }

    public function permissions()
    {
        return $this->hasManyThrough(Permission::class,Role::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function district()
    {
        return $this->belongsTo(District::class);
    }
    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }

    public function trashedNotes()
    {
        return $this->hasMany(Post::class,'user_id')->whereOrigin('App\Notes')->onlyTrashed();
    }

    public function starredNotes()
    {
        return $this->belongsToMany(Post::class);
    }

    public function draftNotes()
    {
        return $this->hasMany(Post::class,'user_id')->where('status','draft')->where('origin','App\Notes');
    }
    public function publishedNotes()
    {
        return $this->hasMany(Post::class,'user_id')->whereStatus('published')->whereOrigin('App\Notes');
    }
    public function recipient()
    {
        return $this->hasMany(Recipient::class);
    }
    public function inboxMessages()
    {
        return $this->hasMany(Recipient::class,'user_id') ;
    }

    public function draftMessages()
    {
        return $this->hasMany(Sender::class,'user_id')->whereStatus('draft');
    }
    public function sentMessages()
    {
        return $this->hasMany(Sender::class,'user_id')->whereStatus('sent');
    }

    public function trashedNews()
    {
        return $this->hasMany(Post::class)->whereOrigin('App\News')->onlyTrashed();
    }
    public function starredNews()
    {
        return $this->belongsToMany(Post::class);
    }
    public function draftNews()
    {
        return $this->hasMany(Post::class)->whereStatus('draft')->whereOrigin('App\News');
    }
    public function publishedNews()
    {
        return $this->hasMany(Post::class)->whereStatus('Published')->whereOrigin('App\News');
    }
    public function register()
    {
        return $this->hasMany(Attendance::class,'monitor_id');
    }
    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }
    public function task()
    {
        return $this->hasMany(Task::class) ;
    }
    public function logs()
    {
        return $this->hasMany(Logs::class);
    }

    public function bursary()
    {
        return $this->belongsToMany(Bursary::class);
    }

    public function loads()
    {
        return $this->hasMany(Timetable::class);
    }
    public function results()
    {
        return $this->hasMany(Exam::class,'user_id');
    }
    public function usermeta()
    {
        return $this->hasMany(Usermeta::class);
    }

    public function aggregate()
    {
        return $this->hasOne(Aggregate::class);
    }

    public function selection()
    {
        return $this->belongsToMany(University::class)->withPivot(['years']);
    }

    public function scopeUsername($query,$username)
    {
        return $query->whereUsername($username);
    }



}
