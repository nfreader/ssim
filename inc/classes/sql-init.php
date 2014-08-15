<?php 

$db = new database();
$db->query(CREATE  VIEW `fullpilotdata` AS SELECT
   `ssim_pilot`.`id` AS `id`,
   `ssim_pilot`.`name` AS `name`,
   `ssim_pilot`.`user` AS `user`,
   `ssim_pilot`.`syst` AS `syst`,
   `ssim_pilot`.`spob` AS `spob`,
   `ssim_pilot`.`ship` AS `ship`,
   `ssim_pilot`.`vessel` AS `vessel`,
   `ssim_pilot`.`homeworld` AS `homeworld`,
   `ssim_pilot`.`credits` AS `credits`,
   `ssim_pilot`.`legal` AS `legal`,
   `ssim_pilot`.`govt` AS `govt`,
   `ssim_pilot`.`fuel` AS `fuel`,
   `ssim_pilot`.`status` AS `status`,
   `ssim_pilot`.`armordam` AS `armordam`,
   `ssim_pilot`.`shielddam` AS `shielddam`,
   `ssim_pilot`.`timestamp` AS `timestamp`,
   `ssim_pilot`.`lastjump` AS `lastjump`,
   `ssim_pilot`.`jumpeta` AS `jumpeta`,
   `ssim_pilot`.`fingerprint` AS `fingerprint`,
   `ssim_spob`.`name` AS `planet`,
   `ssim_spob`.`type` AS `spobtype`,
   `ssim_syst`.`name` AS `system`,
   `ssim_govt`.`name` AS `government`,
   `ssim_govt`.`isoname` AS `isoname`,
   `ssim_govt`.`color` AS `color`,
   `ssim_govt`.`color2` AS `color2`,
   `ssim_ship`.`fueltank` AS `fueltank`,
   `ssim_ship`.`name` AS `shipname`,
   `ssim_ship`.`class` AS `class`,
   `ssim_ship`.`shipwright` AS `shipwright`,(((`ssim_ship`.`shields` - `ssim_pilot`.`shielddam`) / `ssim_ship`.`shields`) * 100) AS `shields`,(((`ssim_ship`.`armor` - `ssim_pilot`.`armordam`) / `ssim_ship`.`armor`) * 100) AS `armor`,((`ssim_pilot`.`fuel` / `ssim_ship`.`fueltank`) * 100) AS `fuelmeter`,
   `ssim_ship`.`cargobay` AS `cargobay`,(case when isnull(sum(`ssim_cargopilot`.`amount`)) then 0 else sum(`ssim_cargopilot`.`amount`) end) AS `cargo`,(case when isnull(sum(`ssim_cargopilot`.`amount`)) then `ssim_ship`.`cargobay` else (`ssim_ship`.`cargobay` - sum(`ssim_cargopilot`.`amount`)) end) AS `capacity`,(case when isnull(((sum(`ssim_cargopilot`.`amount`) / `ssim_ship`.`cargobay`) * 100)) then 0 else ((sum(`ssim_cargopilot`.`amount`) / `ssim_ship`.`cargobay`) * 100) end) AS `cargometer`,(unix_timestamp(`ssim_pilot`.`jumpeta`) - unix_timestamp(now())) AS `remaining`
FROM (((((`ssim_pilot` left join `ssim_spob` on((`ssim_pilot`.`spob` = `ssim_spob`.`id`))) left join `ssim_syst` on((`ssim_pilot`.`syst` = `ssim_syst`.`id`))) left join `ssim_ship` on((`ssim_pilot`.`ship` = `ssim_ship`.`id`))) left join `ssim_govt` on((`ssim_pilot`.`govt` = `ssim_govt`.`id`))) left join `ssim_cargopilot` on((`ssim_pilot`.`id` = `ssim_cargopilot`.`pilot`))););
$db->execute();
$db->query()