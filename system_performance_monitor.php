<?php


$mem = getmeminfo();
$cpu = getcpustat();
$disk = getdiskusage();
$sysinfo = array('CPUSTAT' => $cpu,'MEMINFO' => $mem,'DISKINFO' => $disk);
echo json_encode($sysinfo);

/* DESK STAT*/
function getdiskusage()
{
    exec('df', $dfoutput);
    foreach ($dfoutput as $key => $value) {
        if (preg_match('/(\S+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)%\s+(\S+)/', $value, $matches)) {
            if ($matches[1] == 'Filesystem') {
                /* Pass the head line . This will never run while there was a if_preg_match*/
            continue;
            }
            $tmp = array('1K-blocks' => (int) $matches[2],
            'Used' => (int) $matches[3],
            'Avaliable' => (int) $matches[4],
            'Used_percent' => (int) $matches[5],
            'Mounted_on' => $matches[6], );

            $diskfilesystem[$matches[1]] = $tmp;
        }
    }

    return $diskfilesystem;
}

/* MEMORY STAT*/
function getmeminfo()
{
    exec('cat /proc/meminfo |grep "MemTotal\|MemFree\|SwapTotal\|SwapFree" ', $procmeminfo);
    foreach ($procmeminfo as $key => $value) {
        preg_match('/(\w+):\s+(\d+) /', $value, $matches);
        $meminfo[$matches[1]] = (int) $matches[2];
    }

    return $meminfo;
}

/* CPU STAT*/
function getcpustat()
{
    exec('nproc', $nproc);
    $nproc = $nproc[0];
    exec('cat /proc/stat|grep "^cpu"', $procstat);
//need to rewrite
    foreach ($procstat as $key => $value) {
        $exploded = explode(' ', $value);
        $i = $exploded[0];

        //$tmp = array( 'USER' => (int) $exploded[1],'NICE' = (int) $exploded[2]);

        $cpustat[$i]['USER'] = (int) $exploded[1];
        $cpustat[$i]['NICE'] = (int) $exploded[2];
        $cpustat[$i]['SYS'] = (int) $exploded[3];
        $cpustat[$i]['IDLE'] = (int) $exploded[4];
        $cpustat[$i]['IOWAIT'] = (int) $exploded[5];
        $cpustat[$i]['IRQ'] = (int) $exploded[6];
        $cpustat[$i]['SIRQ'] = (int) $exploded[7];
        $cpustat[$i]['TOTAL'] = array_sum($cpustat[$i]);
    }

    return $cpustat;
}
