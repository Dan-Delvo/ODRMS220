<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionModel extends Model
{
    use HasFactory;

    // Define the table name if it doesn't follow Laravel's naming convention
    protected $table = 'permission';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'name',
        'slug',
        'groupBy',
        'created_at',
        'updated_at',
    ];

    // Disable timestamps if the table doesn't use them
    public $timestamps = false;


    static public function getRecord()
    {
        $getPermission = PermissionModel::groupBy('groupBy')->get();
        $result = array();
        foreach ($getPermission as $value)
        {
            $getPermissionGroup = PermissionModel::getPermissionGroup($value->groupBy);
            $data = array();
            $data['id'] = $value->id;
            $data['name'] = $value->name;
            $group = array();
            foreach($getPermissionGroup as $valueG)
            {
                $dataG = array();
                $dataG['id'] = $valueG->id;
                $dataG['name'] = $valueG->name;
                $group[] = $dataG;
            }
            $data['group'] = $group;
            $result[] = $data;
        }
        return $result;
    }

    static public function getPermissionGroup($groupBy)
    {
        return PermissionModel::where('groupBy', '=', $groupBy)->get();
    }
}
