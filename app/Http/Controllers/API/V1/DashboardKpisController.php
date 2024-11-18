<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SearchOperationDebtResource;
use App\Models\Appointment;
use App\Models\Operation;
use App\Models\Patient;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardKpisController extends Controller
{
    public function getAppointments()
    {

        $userPreference = 'monthly';/* UserPreference::where('doctor_id', $user->id)->pluck('kpi_date')->first(); */

        $startDate = null;
        $endDate = null;
        switch ($userPreference) {
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'daily':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
        }
        $data = Appointment::whereBetween('created_at', [$startDate, $endDate])->count();

        return response()->json(['data' => $data]);
    }

    public function getCanceledAppointments()
    {

        $userPreference = 'monthly';

        $startDate = null;
        $endDate = null;
        switch ($userPreference) {
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'daily':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
        }
        $data = Appointment::whereBetween('created_at', [$startDate, $endDate])->withTrashed()->whereNotNull('deleted_at')->count();
        return response()->json(['data' => $data]);
    }
    public function getDates($type)
    {
        switch ($type) {
            case "week":
                return [
                    Carbon::now()->startOfWeek(Carbon::MONDAY),
                    Carbon::now()->endOfWeek(Carbon::SUNDAY),
                    [
                        __('Monday') => 0,
                        __('Tuesday') => 0,
                        __('Wednesday') => 0,
                        __('Thursday') => 0,
                        __('Friday') => 0,
                        __('Saturday') => 0,
                        __('Sunday') => 0,
                    ]
                ];
            case "month":
                $month = Carbon::now()->format('m');
                $year = Carbon::now()->format('Y');
                $firstDay = mktime(0, 0, 0, $month, 1, $year);
                $daysInMonth = (int) date('t', $firstDay);
                $dayOfWeek = (int) date('w', $firstDay);
                $weekOffset = ($dayOfWeek === 0) ? 6 : $dayOfWeek - 1;
                $count = (int) ceil(($daysInMonth + $weekOffset) / 7);
                $weeks = [];
                for ($i = 1; $i <= $count; $i++) {
                    $weeks[__('Week') . ' ' . $i] = 0;
                }
                return [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth(),
                    $weeks
                ];
            case "year":
                return [
                    Carbon::now()->startOfYear(),
                    Carbon::now()->endOfYear(),
                    [
                        __('January') => 0,
                        __('February') => 0,
                        __('March') => 0,
                        __('April') => 0,
                        __('May') => 0,
                        __('June') => 0,
                        __('July') => 0,
                        __('August') => 0,
                        __('September') => 0,
                        __('October') => 0,
                        __('November') => 0,
                        __('December') => 0,
                    ]
                ];
        }
    }

    public function formatWeek($datestr)
    {
        $date = new \DateTime($datestr);
        $dayOfWeek = $date->format('N');
        $dayOfMonth = $date->format('j');
        $startDayOfWeek = (new \DateTime($date->format('Y-m-01')))->format('N');
        return (int) ceil(($dayOfMonth + $startDayOfWeek - $dayOfWeek) / 7);
    }

    public function groupKey($model, $type)
    {
        switch ($type) {
            case 'week':
                return __($model->created_at->format('l'));
            case 'month':
                return __('Week') . ' ' . $this->formatWeek($model->created_at->format('Y-m-d'));
            case 'year':
                return __($model->created_at->format('F'));
        }
    }

    public function getMonthlyCanceledAppointments()
    {

        $userPreference = 'month';

        [$start, $end, $columns] = $this->getDates($userPreference);
        Appointment::withTrashed()->whereBetween('created_at', [$start, $end])->onlyTrashed()->get()
            ->groupBy(function ($carry) use ($userPreference) {
                return $this->groupKey($carry, $userPreference);
            })
            ->map(function ($group) {
                return $group->count();
            })
            ->each(function ($item, $key) use (&$columns) {
                $columns[$key] = $item;
            });
        return response()->json([
            'data' => $columns,
        ]);
    }
    public function getMonthlyAppointments()
    {

        $userPreference = 'month';


        [$start, $end, $columns] = $this->getDates($userPreference);
        Appointment::whereBetween('created_at', [$start, $end])->get()
            ->groupBy(function ($carry) use ($userPreference) {
                return $this->groupKey($carry, $userPreference);
            })
            ->map(function ($group) {
                return $group->count();
            })
            ->each(function ($item, $key) use (&$columns) {
                $columns[$key] = $item;
            });
        return response()->json([
            'data' => $columns,
        ]);
    }
    public function getTotalRevenue()
    {
        $userPreference = 'month';

        [$currentStart, $currentEnd, $currentColumns] = $this->getDates($userPreference);

        $oldStart = null;
        $oldEnd = null;
        $oldColumns = array_map(function ($item) {
            return $item;
        }, $currentColumns);

        switch ($userPreference) {
            case 'week':
                $oldStart = Carbon::parse($currentStart)->subWeek(1);
                $oldEnd = Carbon::parse($currentEnd)->subWeek(1);
                break;
            case 'month':
                $oldStart = Carbon::parse($currentStart)->subMonth(1);
                $oldEnd = Carbon::parse($currentEnd)->subMonth(1);
                break;
            case 'year':
                $oldStart = Carbon::parse($currentStart)->subYear(1);
                $oldEnd = Carbon::parse($currentEnd)->subYear(1);
                break;
        }

        Operation::whereBetween('created_at', [$currentStart, $currentEnd])->get()
            ->groupBy(function ($carry) use ($userPreference) {
                return $this->groupKey($carry, $userPreference);
            })
            ->map(function ($group) {
                return $group->sum('total_cost');
            })
            ->each(function ($item, $key) use (&$currentColumns) {
                $currentColumns[$key] = $item;
            });

        Operation::whereBetween('created_at', [$oldStart, $oldEnd])->get()
            ->groupBy(function ($carry) use ($userPreference) {
                return $this->groupKey($carry, $userPreference);
            })
            ->map(function ($group) {
                return $group->sum('total_cost');
            })
            ->each(function ($item, $key) use (&$oldColumns) {
                $oldColumns[$key] = $item;
            });




        return response()->json([
            'data' => [$oldColumns, $currentColumns],
        ]);
    }
    public function OnlyCashierNumber()
    {
        $userPreference = 'month';


        [$start, $end, $columns] = $this->getDates($userPreference);

        // Retrieve payments between the specified dates
        $totalPayment = Payment::with('operation')->whereBetween('created_at', [$start, $end])->get()->sum('amount_paid');

        return response()->json([
            'data' => $totalPayment,
        ]);
    }
    public function retrieveFromCashier()
    {

        $userPreference = 'month';

        [$start, $end, $columns] = $this->getDates($userPreference);
        /* 
        $payements = Payement::whereHas('operation', function ($query) use ($id, $start, $end) {
            $query->where('doctor_id', $id)->whereBetween('created_at', [$start, $end]);
        })
            ->get(); */
        $totalPayment = Payment::with('operation')->whereBetween('created_at', [$start, $end])->get();
        $totalPayment
            ->groupBy(function ($carry) use ($userPreference) {
                return $this->groupKey($carry, $userPreference);
            })
            ->map(function ($group) {
                return $group->sum('amount_paid');
            })
            ->each(function ($item, $key) use (&$columns) {
                $columns[$key] = $item;
            });

        return response()->json([
            'data' => $columns,
        ]);
    }
    public function calculateAgePercentage()
    {

        $ageGroups = Patient::selectRaw('CASE 
                                WHEN TIMESTAMPDIFF(YEAR, date, CURDATE()) <= 20 THEN "0-20" 
                                WHEN TIMESTAMPDIFF(YEAR, date, CURDATE()) <= 30 THEN "21-30" 
                                WHEN TIMESTAMPDIFF(YEAR, date, CURDATE()) <= 40 THEN "31-40" 
                                WHEN TIMESTAMPDIFF(YEAR, date, CURDATE()) <= 50 THEN "41-50" 
                                WHEN TIMESTAMPDIFF(YEAR, date, CURDATE()) <= 60 THEN "51-60" 
                                ELSE "61+" 
                             END as age_group, COUNT(*) as count')
            ->groupBy('age_group')
            ->orderByRaw('CAST(SUBSTRING(age_group, 1, 2) AS SIGNED)')
            ->get();

        $totalPatients = Patient::count();

        $percentageData = $ageGroups->map(function ($group) use ($totalPatients) {

            return [
                'age_group' => $group->age_group,
                'count' => $group->count,

            ];
        });

        return response()->json(['data' => $percentageData]);
    }
    public function TotalPatients()
    {

        $data = Patient::count();
        return response()->json(['data' => $data]);
    }
    /* public function appointmentKpipeak()
    {
       
        $appointments = Appointment::where('doctor_id', $id)->latest()->with('patient')->take(5)->get();

        return response()->json([
            'data' => AppointmentKpi::collection($appointments),
        ]);
    } */
    public function PatientsDebt(Request $request)
    {



        $Operations = Operation::with('patient', 'operationdetails', 'payments')->where('is_paid', 0)->whereBetween('created_at', [Carbon::parse($request->date)->startOfDay(),  Carbon::parse($request->date2)->endOfDay()])->get();
        return   SearchOperationDebtResource::collection($Operations);
    }
}
