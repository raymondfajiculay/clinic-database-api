<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class IndicatorController extends Controller
{
    public function contraceptive_users(Request $request)
    {
        // Extract query parameters with defaults if needed
        $startDate = $request->query('startDate'); 
        $endDate = $request->query('endDate'); 
        $startAge = $request->query('startAge'); 
        $endAge = $request->query('endAge'); 
        $clinic = $request->query('clinic');
        
        // Start with the base query
        $sql = "
            SELECT 
                COUNT(*) AS Total
            FROM 
                fpclient AS fpc
            LEFT JOIN 
                client AS c ON fpc.client_id = c.id
        ";
        
        // Initialize an empty array for additional conditions
        $whereClauses = [];
    
        // Add conditions to the WHERE clause based on query parameters
        if ($startAge) {
            $whereClauses[] = "TIMESTAMPDIFF(YEAR, c.dob, fpc.created_at) >= :startAge"; 
        }
    
        if ($endAge) {
            $whereClauses[] = "TIMESTAMPDIFF(YEAR, c.dob, fpc.created_at) <= :endAge";
        }
    
        if ($startDate) {
            $whereClauses[] = "fpc.created_at >= :startDate"; 
        }
    
        if ($endDate) {
            $whereClauses[] = "fpc.created_at <= :endDate";
        }
    
        if ($clinic) {
            $whereClauses[] = "fpc.clinic_id = :clinic"; 
        }
    
        // Construct the WHERE clause if there are any conditions
        if (count($whereClauses) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses); 
        }
    
        // Prepare parameters for the SQL query
        $params = [
            'startAge' => $startAge,
            'endAge' => $endAge,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'clinic' => $clinic,
        ];
    
        // Execute the SQL query with parameterized values
        $contraceptive_users = DB::select($sql, array_filter($params, function ($value) {
            return !is_null($value);
        })); // Exclude null values to avoid errors
    
        // Return the result as a JSON response
        return response()->json($contraceptive_users);
    }

    public function contraceptive_referrals() { 
        // Start with the base query
        $sql = "
        SELECT COUNT(DISTINCT client_id) AS Total
            FROM (
            SELECT client.id AS client_id
            FROM `fprecord_supply_implant`
            JOIN `fprecord` ON `fprecord`.`id` = `fprecord_supply_implant`.`fprecord_id`
            JOIN `fpclient` ON `fpclient`.`id` = `fprecord`.`fpclient_id`
            JOIN `client` ON `client`.`id` = `fpclient`.`client_id`
            LEFT JOIN `advocate` ON `advocate`.`id` = `client`.`advocate_id`
            WHERE `fprecord_supply_implant`.`arm` != 'No'
                AND `client`.`advocate_id` IS NOT NULL AND `client`.`advocate_id` > 0
                AND `fprecord`.`compensation_status` IN ('Paid', 'Unpaid')
                AND `client`.`id` > 0

        UNION ALL

        SELECT client.id AS client_id
            FROM `fprecord_supply_iud`
            JOIN `fprecord` ON `fprecord`.`id` = `fprecord_supply_iud`.`fprecord_id`
            JOIN `fpclient` ON `fpclient`.`id` = `fprecord`.`fpclient_id`
            JOIN `client` ON `client`.`id` = `fpclient`.`client_id`
            LEFT JOIN `advocate` ON `advocate`.`id` = `client`.`advocate_id`
            WHERE `fprecord_supply_iud`.`amount` > 0
                AND `client`.`advocate_id` IS NOT NULL AND `client`.`advocate_id` > 0
                AND `fprecord`.`compensation_status` IN ('Paid', 'Unpaid')
                AND `client`.`id` > 0
            
        UNION ALL
            
        SELECT client.id AS client_id
            FROM `fprecord_supply_pill`
            JOIN `fprecord` ON `fprecord`.`id` = `fprecord_supply_pill`.`fprecord_id`
            JOIN `fpclient` ON `fpclient`.`id` = `fprecord`.`fpclient_id`
            JOIN `client` ON `client`.`id` = `fpclient`.`client_id`
            LEFT JOIN `advocate` ON `advocate`.`id` = `client`.`advocate_id`
            WHERE `fprecord_supply_pill`.`amount` > 0
                AND `client`.`advocate_id` IS NOT NULL AND `client`.`advocate_id` > 0
                AND `fprecord`.`compensation_status` IN ('Paid', 'Unpaid')
                AND `client`.`id` > 0
            
        UNION ALL
            
            SELECT client.id AS client_id
            FROM `fprecord_supply_injectable`
            JOIN `fprecord` ON `fprecord`.`id` = `fprecord_supply_injectable`.`fprecord_id`
            JOIN `fpclient` ON `fpclient`.`id` = `fprecord`.`fpclient_id`
            JOIN `client` ON `client`.`id` = `fpclient`.`client_id`
            LEFT JOIN `advocate` ON `advocate`.`id` = `client`.`advocate_id`
            WHERE `fprecord_supply_injectable`.`amount` > 0
                AND `client`.`advocate_id` IS NOT NULL AND `client`.`advocate_id` > 0
                AND `fprecord`.`compensation_status` IN ('Paid', 'Unpaid')
                AND `client`.`id` > 0
            ) AS combined
        ";
    
        // Execute the SQL query with parameterized values
        $contraceptive_referrals = DB::select($sql); // Exclude null values to avoid errors
    
        // Return the result as a JSON response
        return response()->json($contraceptive_referrals);
    }

    public function barangay_accessing_services() {
        $query = "
        SELECT COUNT(DISTINCT c.barangay_id) as Total
        FROM fprecord fpr
            JOIN fpclient fpc ON fpc.id = fpr.fpclient_id
            JOIN client c ON fpc.client_id = c.id
        WHERE fpr.clinic_id = 2
        ";
    
        // Execute the SQL query with parameterized values
        $result = DB::select($query); // Exclude null values to avoid errors
    
        // Return the result as a JSON response
        return response()->json($result);
    }

    public function screened_for_hiv() {
        $query = "
            SELECT  COUNT(DISTINCT hivclient.id) as Total
            FROM hivclient
            WHERE YEAR(hivclient.created_at) - YEAR(hivclient.dob) - (DATE_FORMAT(hivclient.created_at, '%m%d') < DATE_FORMAT(hivclient.dob, '%m%d')) >= 24
        ";

        $result = DB::select($query);

        return response()->json($result);
    }

    public function couple_years_protected() {
        $query = "
            SELECT COUNT(*) AS Total
            FROM `reports_ca_latency`
                JOIN `fprecord` ON `fprecord`.`id` = `reports_ca_latency`.`fprecord_id`
                JOIN `fpclient` ON `fpclient`.`id` = `fprecord`.`fpclient_id`
                JOIN `client` ON `client`.`id` = `fpclient`.`client_id`
                JOIN `activity` ON `activity`.`id` = `fprecord`.`activity_id`
            WHERE `client`.`deleted_at` IS NULL
                AND `fpclient`.`deleted_at` IS NULL
                AND `fprecord`.`deleted_at` IS NULL
                AND (`client`.`id` >0)
                AND `reports_ca_latency`.`actual` >0
        ";

        $result = DB::select($query);

        return response()->json($result);
    }

    public function modern_contraceptive_user() {
        $query = "
            SELECT COUNT(DISTINCT c.id) as Total
            FROM fpclient fpc
                JOIN client c ON fpc.client_id = c.id
            WHERE
                c.sex = 'female';
        ";

        $result = DB::select($query);

        return response()->json($result);
    }
}