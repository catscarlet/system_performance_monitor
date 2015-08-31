<?php

$time = time();
$mem = getmeminfo();
$cpu = getcpustat();
$disk = getdiskusage();
$psinfo = getps();

$sysinfo = array(
  'TIME' => $time,
  'CPUSTAT' => $cpu,
  'MEMINFO' => $mem,
  'DISKINFO' => $disk,
  'PSINFO' => $psinfo
);

$sysinfo_json = json_encode($sysinfo);
$filepath = 'monitor.json';
//echo $sysinfo_json;
sysinfosave($filepath, $sysinfo_json);
//sysinfoecho($filepath);

/*ECHO FROM FILE*/
function sysinfoecho($filepath)
{
    if (file_get_contents($filepath)) {
        echo file_get_contents($filepath);
    }
}
/*SAVE TO FILE*/
function sysinfosave($filepath, $sysinfo_json)
{
    $fopen = fopen($filepath, 'a') or die('File error !');
    fwrite($fopen, $sysinfo_json);
    fwrite($fopen, "\n");
    fclose($fopen);
}

/* DISK STAT*/
function getdiskusage()
{
    exec('df', $dfoutput);
    foreach ($dfoutput as $key => $value) {
        if (preg_match('/(\S+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)%\s+(\S+)/', $value, $matches)) {
            if ($matches[1] == 'Filesystem') {
                /* Pass the head line . This will never run while there was a if_preg_match*/
            continue;
            }
            $tmp = array(
              '1K-blocks' => (int) $matches[2],
              'Used' => (int) $matches[3],
              'Avaliable' => (int) $matches[4],
              'Used_percent' => (int) $matches[5],
              'Mounted_on' => $matches[6],
              );

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
    exec('cat /proc/stat|grep "^cpu"|tail -n '.$nproc, $procstat);
//need to rewrite
    foreach ($procstat as $key => $value) {
        $exploded = explode(' ', $value);
        $i = $exploded[0];

        $tmp = array(
          'USER' => (int) $exploded[1],
          'NICE' => (int) $exploded[2],
          'SYS' => (int) $exploded[3],
          'IDLE' => (int) $exploded[4],
          'IOWAIT' => (int) $exploded[5],
          'IRQ' => (int) $exploded[6],
          'SIRQ' => (int) $exploded[7],
          );
        $cpustat[$i] = $tmp;
        $cpustat[$i]['TOTAL'] = array_sum($cpustat[$i]);
    }

    return $cpustat;
}

function getps()
{
    exec('ps auxf', $psinfo);

    return $psinfo;
}
