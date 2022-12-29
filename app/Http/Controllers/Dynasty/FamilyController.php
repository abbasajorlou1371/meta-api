<?php

namespace App\Http\Controllers\Dynasty;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dynasty\FamilyMemberResource;
use App\Models\Dynasty\Dynasty;
use App\Models\Dynasty\Family;
use Illuminate\Http\Request;

class FamilyController extends Controller
{
    public function index(Dynasty $dynasty ,Family $family)
    {
        return FamilyMemberResource::collection($family->familyMembers);
    }
}
