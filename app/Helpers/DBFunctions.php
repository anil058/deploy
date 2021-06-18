<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\ClubAchiever;
use App\Models\ClubMaster;
use App\Models\LevelAchiever;
use App\Models\LevelMaster;
use App\Models\Member;
use App\Models\MemberDeposit;
use App\Models\MemberIncome;
use App\Models\MemberUser;
use App\Models\MemberMap;
use App\Models\MemberRewards;
use App\Models\MemberWallet;

class DBFunctions{
    // Hierarchical Data ======================================================
    public function dbFuncGetParents($member_id){
        // DB::enableQueryLog();
        $sql="SELECT m.member_id,m.unique_id, concat(m.first_name,' ',m.last_name) child_name,CONCAT(m1.first_name,' ',m1.last_name) parent_name, m.designation_id,p.level_ctr
            FROM member_maps p
            INNER JOIN members m ON m.member_id=p.parent_id
            INNER JOIN members m1 ON m.parent_id=m1.member_id
            WHERE p.level_ctr < 12 and p.member_id=".$member_id;;
        $records = DB::select($sql);

        $arrayRec = array();
        foreach ($records as $refTable){
            $arrayRec[] = $refTable;
        }
        return $arrayRec;
    }

    public function dbFuncGetChildren($member_id){
        $sql="SELECT m.member_id,m.unique_id, concat(m.first_name,' ',m.last_name) child_name,CONCAT(m1.first_name,' ',m1.last_name) parent_name, m.designation_id,p.level_ctr
            FROM member_maps p
            INNER JOIN members m ON m.member_id=p.member_id
            INNER JOIN members m1 ON m.parent_id=m1.member_id
            WHERE p.parent_id=".$member_id;
        $records = DB::select($sql);

        $arrayRec = array();
        foreach ($records as $refTable){
            $arrayRec[] = $refTable;
        }
        return $arrayRec;
    }


    //Get All Achiever Records ==============================================
    public function getLevelAchievers($level){
        $sql="SELECT m.unique_id, concat(m.first_name,' ',m.last_name) child_name, CONCAT(m1.first_name,' ',m1.last_name) parent_name, m.designation_id
        FROM members m
        INNER JOIN members m1 ON m.parent_id=m1.member_id
        WHERE m.designation_id=".$level;
        $records = DB::select($sql);

        $arrayRec = array();
        foreach ($records as $refTable){
            $arrayRec[] = $refTable;
        }
        return $arrayRec;
    }

    public function dbFuncGetAllBronzAchievers(){
        $arrRet = $this->getLevelAchievers(2);
        return $arrRet;
    }

    public function dbFuncGetAllSilverAchievers(){
        $arrRet = $this->getLevelAchievers(3);
        return $arrRet;
    }

    public function dbFuncGetAllGoldAchievers(){
        $arrRet = $this->getLevelAchievers(4);
        return $arrRet;
    }

    public function dbFuncGetAllDiamondAchievers(){
        $arrRet = $this->getLevelAchievers(5);
        return $arrRet;
    }

    public function dbFuncGetAllRoyaltyAchievers(){
        $arrRet = $this->getLevelAchievers(6);
        return $arrRet;
    }

    public function dbFuncGetAllAchievementsOfAMember($member){
        $sql="SELECT DISTINCT designation_id
            FROM club_achievers WHERE member_id=".$member;
        $records = DB::select($sql);

        $arrayRec = array();
        foreach ($records as $refTable){
            $arrayRec[] = $refTable;
        }
        return $arrayRec;
    }

    //Get Uplink Level Achiever Records =====================================
    /* Get No of members in each level for members who are parents of given member*/

    public function dbFuncLevelWiseParentsChildrenCount($member_id){
        $sql="SELECT p.parent_id,p.level_ctr,count(p.member_id) total_members
            FROM member_maps p
            INNER JOIN members m ON p.parent_id=m.member_id
            WHERE p.parent_id IN (SELECT p.parent_id FROM member_maps p WHERE p.parent_id>0 and p.member_id=".$member_id.")
            GROUP BY p.parent_id,p.level_ctr";

        $records = DB::select($sql);
        $arrayRec = array();
        foreach ($records as $refTable){
            $arrayRec[] = $refTable;
        }
        return $arrayRec;
    }

    public function dbFuncLevelWiseChildrenCount($member_id){
        $sql="SELECT p.parent_id,p.level_ctr,count(p.member_id) total_members
            FROM member_maps p
            INNER JOIN members m ON p.parent_id=m.member_id
            WHERE p.parent_id=".$member_id." GROUP BY p.parent_id,p.level_ctr";

        $records = DB::select($sql);
        $arrayRec = array();
        foreach ($records as $refTable){
            $arrayRec[] = $refTable;
        }
        return $arrayRec;
    }

    /** Get designation wise total members of parents of given member */
    public function dbFuncDesignationWiseParentsChildrenCount($member_id){
        $sql="SELECT m.parent_id,a.designation_id,COUNT(a.designation_id) AS total_members
        FROM club_achievers a
        INNER JOIN members m ON a.member_id=m.member_id
        WHERE m.parent_id IN (SELECT p.parent_id FROM member_maps p WHERE p.parent_id>0 and p.member_id=".$member_id.")
        GROUP BY m.parent_id,a.designation_id";

        $records = DB::select($sql);
        $arrayRec = array();
        foreach ($records as $refTable){
            $arrayRec[] = $refTable;
        }
        return $arrayRec;

    }

    public function dbFuncDesignationWiseChildrenCount($member_id){
        $sql="SELECT m.parent_id,a.designation_id,COUNT(a.designation_id) AS total_members
        FROM club_achievers a
        INNER JOIN members m ON a.member_id=m.member_id
        WHERE m.parent_id =".$member_id."
        GROUP BY m.parent_id,a.designation_id";

        $records = DB::select($sql);
        $arrayRec = array();
        foreach ($records as $refTable){
            $arrayRec[] = $refTable;
        }
        return $arrayRec;

    }

    public function dbFuncMemberRewards($member_id){
        return MemberRewards::where('member_id',$member_id)->get();
    }


    //dbFuncGetUpLevelBronzes
    public function getUplinkLevelAchievers($member, $designation){
        $sql="SELECT p.level_ctr, concat(m.first_name,' ',m.last_name) member_name, m.designation_id
            FROM club_achievers a
            INNER JOIN members m ON a.member_id=m.member_id
            INNER JOIN member_maps p ON a.member_id=p.member_id
            WHERE p.member_id=".$member." AND a.designation_id=".$designation.
            " ORDER BY p.level_ctr";

        $records = DB::select($sql);

        $arrayRec = array();
        foreach ($records as $refTable){
            $arrayRec[] = $refTable;
        }
        return $arrayRec;
    }


    public function getCompanyLevelAchievers($designation){
        $sql="SELECT a.member_id, p.level_ctr, concat(m.first_name,' ',m.last_name) member_name, m.designation_id
            FROM club_achievers a
            INNER JOIN members m ON a.member_id=m.member_id
            INNER JOIN member_maps p ON a.member_id=p.member_id
            WHERE a.designation_id=".$designation;

        $records = DB::select($sql);

        $arrayRec = array();
        foreach ($records as $refTable){
            $arrayRec[] = $refTable;
        }
        return $arrayRec;
    }


    //Get Masters
    public function dbFuncGetLevelMasterRecords(){

    }

    public function dbFuncGetClubMasterRecords(){

    }

    public function dbFuncGetTeamBorozAchievers($member_id){

    }

    public function dbFuncGetTeamSilverAchievers($member_id){

    }

    public function dbFuncGetTeamGoldAchievers($member_id){

    }

    public function dbFuncGetTeamDiamondAchievers($member_id){

    }

    public function dbFuncGetTeamRoyaltyAchievers($member_id){

    }

}


