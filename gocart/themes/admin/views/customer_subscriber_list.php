<?php

header('Content-Type: "text/csv"');
header('Content-Disposition: attachment; filename="email_subscribers_list.csv"');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header("Content-Transfer-Encoding: binary");
header('Pragma: public');

echo $sub_list;