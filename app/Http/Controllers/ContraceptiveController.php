<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContraceptiveController extends Controller
{
    public function client_record()
    {
        $clients = DB::select("
        SELECT  
            fpc.id AS FP_Client_ID, 
            cli.id AS Client_ID, 
            cli.first_name AS First_Name, 
            cli.middle_name AS Middle_Name, 
            cli.last_name AS Last_Name,
            DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), cli.dob)), '%Y') + 0 AS Age,
            cli.sex AS Sex,
            cli.contact_no AS Contact_Number,
            fpc.type_of_client AS Type_Of_Client,
            p.name AS Purok,
            b.name AS Barangay,
            m.name AS Municipality
        FROM fpclient fpc
        LEFT JOIN client cli ON fpc.client_id = cli.id
        LEFT JOIN barangay b ON cli.barangay_id = b.id
        LEFT JOIN municipality m ON cli.municipality_id = m.id
        LEFT JOIN purok p ON cli.purok_id = p.id
        ORDER BY fpc.id
        ");

        return response()->json($clients);
    }

    public function fp_record() {
        $fprecord = DB::select("
            SELECT fp.id AS FP_Record_ID, 
                fpc.id AS FP_CLient_ID, 
                cli.id AS Client_ID, 
                cli.first_name as First_Name, 
                cli.last_name as Last_Name, 
                fp.medical_findings as Medical_Findings, 
                fp.method as Method, 
                cln.name as Clinic, 
                activity.category AS Activity, 
                fp.date as Date
            FROM fprecord fp
            LEFT JOIN fpclient fpc ON fp.fpclient_id = fpc.id
            LEFT JOIN client cli ON fpc.client_id = cli.id
            LEFT JOIN clinic cln ON fp.clinic_id = cln.id
            LEFT JOIN activity ON fp.activity_id = activity.id
        ");

        return response()->json($fprecord);
    }

    public function injectable_record() {
        $injectable = DB::select("
            SELECT 
                fprsij.id AS Supply_Record_ID, 
                fpr.id AS FP_Record_ID, 
                fpc.id AS FP_Client_ID, 
                cli.id AS Client_ID, 
                cli.first_name AS First_Name, 
                cli.last_name AS Last_Name, 
                sij.brand AS Brand, 
                amount AS Amount, 
                DATE_FORMAT(fprsij.created_at, '%b %d, %Y') AS Service_Date,
                clinic.name AS Clinic,
                activity.category AS Activity,  
                CASE
                    WHEN (
                        SELECT COUNT(*)
                        FROM reports_ca_latency r
                        WHERE r.fprecord_id = fpr.id AND r.date < rcl.date
                    ) + 1 = 1 THEN 'Actual'
                    ELSE 'Latent'
                END AS Name,
                CASE
                    WHEN (
                        SELECT COUNT(*)
                        FROM reports_ca_latency r
                        WHERE r.fprecord_id = fpr.id AND r.date < rcl.date
                    ) + 1 = 1 THEN 'D'
                    ELSE 'DL'
                END AS Code,
                DATE_FORMAT(rcl.date , '%b %d, %Y') AS Latent
            FROM fprecord_supply_injectable fprsij
            LEFT JOIN supply_injectable sij ON fprsij.supply_injectable_id = sij.id
            LEFT JOIN fprecord fpr ON fpr.id = fprsij.fprecord_id
            LEFT JOIN fpclient fpc ON fpc.id = fpr.fpclient_id
            LEFT JOIN client cli ON fpc.client_id = cli.id
            LEFT JOIN followup fup ON fpr.id = fup.fprecord_id
            LEFT JOIN reports_ca_latency rcl ON fpr.id = rcl.fprecord_id
            LEFT JOIN activity ON fpr.activity_id = activity.id
            LEFT JOIN clinic ON fpr.clinic_id = clinic.id
            ORDER BY fprsij.id, rcl.date
        ");

        return response()->json($injectable);
    }

    public function implant_record() {
        $implant = DB::select("
            SELECT 
                fprsim.id AS Supply_Record_ID, 
                fpr.id AS FP_Record_ID, 
                fpc.id AS FP_Client_ID, 
                cli.id AS Client_ID, 
                cli.first_name AS First_Name, 
                cli.last_name AS Last_Name, 
                sim.brand AS Brand, 
                arm AS Arm, 
                DATE_FORMAT(fpr.created_at, '%b %d, %Y') AS Service_Date,  
                clinic.name AS Clinic,
                activity.category AS Activity,
                CASE
                    WHEN (
                        SELECT COUNT(*)
                        FROM reports_ca_latency r
                        WHERE r.fprecord_id = fpr.id AND r.date <= rcl.date
                    ) = 1 THEN 'Actual'
                    ELSE 'Latent'
                END AS Name,
                CASE
                    WHEN (
                        SELECT COUNT(*)
                        FROM reports_ca_latency r
                        WHERE r.fprecord_id = fpr.id AND r.date <= rcl.date
                    ) = 1 THEN 'I'
                    ELSE 'IL'
                END AS Code,
                DATE_FORMAT(rcl.date , '%b %d, %Y') AS Latent
            FROM fprecord_supply_implant fprsim
            LEFT JOIN supply_implant sim ON fprsim.supply_implant_id = sim.id
            LEFT JOIN fprecord fpr ON fpr.id = fprsim.fprecord_id
            LEFT JOIN fpclient fpc ON fpc.id = fpr.fpclient_id
            LEFT JOIN client cli ON fpc.client_id = cli.id
            LEFT JOIN reports_ca_latency rcl ON fpr.id = rcl.fprecord_id
            LEFT JOIN clinic ON fpr.clinic_id = clinic.id
            LEFT JOIN activity ON fpr.activity_id = activity.id
            ORDER BY  fprsim.id, rcl.date
        ");

        return response()->json($implant);
    }

    public function iud_record() {
        $iud = DB::select("
            SELECT 
                fprsu.id AS Supply_Record_ID, 
                fpr.id AS FP_Record_ID, 
                fpc.id AS FP_Client_ID, 
                cli.id AS Client_ID, 
                cli.first_name AS First_Name, 
                cli.last_name AS Last_Name, 
                su.brand AS Brand, 
                DATE_FORMAT(fpr.created_at, '%b %d, %Y') AS Service_Date, 
                clinic.name AS Clinic,
                activity.category AS Activity,
                CASE
                    WHEN (
                        SELECT COUNT(*)
                        FROM reports_ca_latency r
                        WHERE r.fprecord_id = fpr.id AND r.date <= rcl.date
                    ) = 1 THEN 'Actual'
                    ELSE 'Latent'
                END AS Name,
                CASE
                    WHEN (
                        SELECT COUNT(*)
                        FROM reports_ca_latency r
                        WHERE r.fprecord_id = fpr.id AND r.date <= rcl.date
                    ) = 1 THEN 'D'
                    ELSE 'DL'
                END AS Code,
                DATE_FORMAT(rcl.date , '%b %d, %Y') AS Latent
            FROM fprecord_supply_iud fprsu
            LEFT JOIN supply_iud su ON fprsu.supply_iud_id = su.id
            LEFT JOIN fprecord fpr ON fpr.id = fprsu.fprecord_id
            LEFT JOIN fpclient fpc ON fpc.id = fpr.fpclient_id
            LEFT JOIN client cli ON fpc.client_id = cli.id
            LEFT JOIN reports_ca_latency rcl ON fpr.id = rcl.fprecord_id
            LEFT JOIN clinic ON fpr.clinic_id = clinic.id
            LEFT JOIN activity ON fpr.activity_id = activity.id
            ORDER BY  fprsu.id, rcl.date
        ");

        return response()->json($iud);
    }

    public function pill_record() {
        $pill = DB::select("
            SELECT 
                fprsp.id AS Supply_Record_ID, 
                fpr.id AS FP_Record_ID, 
                fpc.id AS FP_Client_ID, 
                cli.id AS Client_ID, 
                cli.first_name AS First_Name, 
                cli.last_name AS Last_Name, 
                sp.brand AS Brand, 
                DATE_FORMAT(fpr.created_at, '%b %d, %Y') AS Service_Date,
                activity.category AS Activity,
                clinic.name AS Clinic
            FROM fprecord_supply_pill fprsp
            LEFT JOIN supply_pill sp ON fprsp.supply_pill_id = sp.id
            LEFT JOIN fprecord fpr ON fpr.id = fprsp.fprecord_id
            LEFT JOIN fpclient fpc ON fpc.id = fpr.fpclient_id
            LEFT JOIN client cli ON fpc.client_id = cli.id
            LEFT JOIN activity ON fpr.activity_id = activity.id
            LEFT JOIN clinic ON fpr.clinic_id = clinic.id
            ORDER BY  fprsp.id
        ");
    
        return response()->json($pill);
    }

    public function condom_record() {
        $condom = DB::select("
            SELECT 
                fprsc.id AS Supply_Record_ID, 
                fpr.id AS FP_Record_ID, 
                fpc.id AS FP_Client_ID, 
                cli.id AS Client_ID, 
                cli.first_name AS First_Name, 
                cli.last_name AS Last_Name, 
                sc.brand AS Brand, 
                DATE_FORMAT(fpr.created_at, '%b %d, %Y') AS Service_Date
            FROM fprecord_supply_condom fprsc
            LEFT JOIN supply_condom sc ON fprsc.supply_condom_id = sc.id
            LEFT JOIN fprecord fpr ON fpr.id = fprsc.fprecord_id
            LEFT JOIN fpclient fpc ON fpc.id = fpr.fpclient_id
            LEFT JOIN client cli ON fpc.client_id = cli.id
            ORDER BY  fprsc.id
        ");

        return response()->json($condom);
    }

    public function supplement_record() {
        $supplement = DB::select("
            SELECT 
                fprsc.id AS Supply_Record_ID, 
                fpr.id AS FP_Record_ID, 
                fpc.id AS FP_Client_ID, 
                cli.id AS Client_ID, 
                cli.first_name AS First_Name, 
                cli.last_name AS Last_Name, 
                sc.brand AS Brand, 
                DATE_FORMAT(fpr.created_at, '%b %d, %Y') AS Service_Date
            FROM fprecord_supply_condom fprsc
            LEFT JOIN supply_condom sc ON fprsc.supply_condom_id = sc.id
            LEFT JOIN fprecord fpr ON fpr.id = fprsc.fprecord_id
            LEFT JOIN fpclient fpc ON fpc.id = fpr.fpclient_id
            LEFT JOIN client cli ON fpc.client_id = cli.id
            ORDER BY  fprsc.id
        ");

        return response()->json($supplement);
    }

    public function services_record() {
        $services = DB::select("
            SELECT 
                fprsc.id AS Supply_Record_ID, 
                fpr.id AS FP_Record_ID, 
                fpc.id AS FP_Client_ID, 
                cli.id AS Client_ID, 
                cli.first_name AS First_Name, 
                cli.last_name AS Last_Name, 
                sc.brand AS Brand, 
                DATE_FORMAT(fpr.created_at, '%b %d, %Y') AS Service_Date
            FROM fprecord_supply_condom fprsc
            LEFT JOIN supply_condom sc ON fprsc.supply_condom_id = sc.id
            LEFT JOIN fprecord fpr ON fpr.id = fprsc.fprecord_id
            LEFT JOIN fpclient fpc ON fpc.id = fpr.fpclient_id
            LEFT JOIN client cli ON fpc.client_id = cli.id
            ORDER BY  fprsc.id
        ");

        return response()->json($services);
    }

    // public function implant_record() {
    //     $implant = DB::select("
    //     ");

    //     return response()->json($implant);
    // }
}
