<?php

/**
 * Gets the current cpu load and usage
 *
 * @return array ['load' => *, 'usage' => *]
 */
function getCPULoad() {
  $str = substr(strrchr(shell_exec("uptime"),":"),1);
  $avs = array_map('floatval', array_map("trim",explode(",",$str)));

  $exec_loads = sys_getloadavg();
  $exec_cores = trim(shell_exec("grep -P '^processor' /proc/cpuinfo|wc -l"));
  $cpu = round($exec_loads[0]/($exec_cores . 1)*100, 0);

  return [
    'load' => $avs,
    'usage' => $cpu
  ];
}

/**
 * Gets the current memory usage
 * @return array ['used' => *, 'total' => *, 'percentage' => *]
 */
function getMemoryUsage(){
  $exec_free = explode("\n", trim(shell_exec('free')));
  $get_mem = preg_split("/[\s]+/", $exec_free[1]);

  return [
    'used' => (float) number_format(round($get_mem[2]/1024, 2), 2),
    'total' => (float) number_format(round($get_mem[1]/1024, 2), 2),
    'percentage' => round($get_mem[2]/$get_mem[1]*100, 2)
  ];
}

/**
 * Gets the uptime in days
 * @return int
 */
function getUptime() {
  $exec_uptime = preg_split("/[\s]+/", trim(shell_exec('uptime')));
  return (int) $exec_uptime[2];
}

/**
 * Gets the current disk space usage
 *
 * @return array ['free' => *, 'total' => *, 'percentage' => *]
 */
function getFreeSpace() {
  $free = disk_free_space("/");
  $total = disk_total_space("/");
  $usage = (100 - round(($free * 100)/ $total, 2));
  return [
    'free' => number_format($free / ( 1024 * 1024 * 1024 ), 2),
    'total' => number_format($total / ( 1024 * 1024 * 1024 ), 2),
    'percentage' => $usage
  ];
}

echo json_encode([
  'cpu' => getCPULoad(),
  'ram' => getMemoryUsage(),
  'uptime' => getUptime(),
  'diskspace' => getFreeSpace()
]);
