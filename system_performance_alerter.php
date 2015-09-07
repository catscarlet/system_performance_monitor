<?php

$filepath = 'monitor.json';
$readcpustathistory = 5;
$threshold_times = 3;//CPU warning threshold of last count
$threshold_percent = 10;//CPU warning threshold of IDLE remain
exec("tail -n $readcpustathistory $filepath", $system_performance_monitor);
$readhistorymax = min(count($system_performance_monitor), $readcpustathistory);
$i = $readhistorymax;
foreach ($system_performance_monitor as $key => $value) {
    --$i;
    $monitor[$i] = json_decode($value, true);
}

$time = $monitor[0]['TIME'];
$error_description = null;
$error_description = $error_description.memcheck($monitor, $readhistorymax);
$error_description = $error_description.dfcheck($monitor);
$error_description = $error_description.cpucheck($monitor, $threshold_times, $threshold_percent);

/* Error Message OUTPUT START*/
    if ($error_description) {
        $error_messages = array('time' => $time ,'error_code' => 1, 'error_description' => $error_description);

        /* send email */
        require_once 'smtp/sendmail.php';
        sendemailbysmtp('Your server may have performance problems', $error_description);

        echo json_encode($error_messages);
    } else {
        $error_messages = array('time' => $time ,'error_code' => 0, 'error_description' => 'Your system running normally.');
        echo json_encode($error_messages);
    }
    echo "\n";
/* Error Message OUTPUT END*/

/* ------------------function------------------ */

function memcheck($monitor, $readhistorymax)
{
    if ($readhistorymax > 1) {
        if ($monitor[0]['MEMFREE']['swap_used'] > $monitor[1]['MEMFREE']['swap_used']) {
            return 'Swap has been used .You system may be out of memory .';
        }
    }
}

function dfcheck($monitor)
{
    foreach ($monitor[0]['DISKINFO'] as $filesystem => $filesysteminfo) {
        if ($filesysteminfo['Used_percent'] > 90) {
            return 'Filesystem "'.$filesystem.' "used more than 90% .';
        }
    }
}

function cpucheck($monitor, $threshold_times, $threshold_percent)
{
    $cpu_warning_count = 0;
    foreach ($monitor as $monitorhistoryID => $monitorhistory) {
        foreach ($monitorhistory['CPUSTAT'] as $cpuid => $cpustat) {
            $cpu_idlepercent = $cpustat['IDLE'] / $cpustat['TOTAL'] * 100;
            if ($cpu_idlepercent < $threshold_percent) {
                /*Add 1 to $cpu_warning_count .if $cpu_warning_count is equal or greater than $threshold  ,it means the warning last $threshold times.*/
                ++$cpu_warning_count;
            }
        }
    }

    if ($cpu_warning_count >= $threshold_times) {
        return 'CPU_IDLE may be less than 10% since last '.$threshold_times.' times check.';
    }
}
