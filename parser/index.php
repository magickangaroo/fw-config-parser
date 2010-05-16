<?php
// Sidewinder Config Parser - Written by JonTheNiceGuy
// Patches supplied by PHPCodeMonkey
require_once("classes/parser.php");

?>
<html>
<head>
  <title>Sidewinder Rulebase Parser</title>
  <style>
    TD.rulegroup {text-align:center;}
    TABLE {width:100%;}
  </style>
</head>
<body>
<!-- Parser status: 
<?php
  $file_dir=dirname(__FILE__) . '/../example_configs/sidewinder/';
  if($dir_handle=opendir($file_dir)) {
    while(false !== ($filename=readdir($dir_handle))) {
      $filename=$file_dir.$filename;
      echo "\r\n$filename";
      if($oldfilename==$filename) {exit(0);}
      $oldfilename=$filename;
      if(!is_dir($filename) and substr($filename, -4)!='.php') {
        $file[basename($filename, '.config')]=str_replace("\r\n", "\n", 
                                                str_replace("     ", " ", 
                                                  str_replace("\\\r", "", 
                                                    str_replace("\\\n", "", 
                                                      str_replace("\\\r\n", "", 
                                                        str_replace("\\\n\r", "", 
                                                          file_get_contents($filename)
                                                        )
                                                      )
                                                    )
                                                  )
                                                )
                                              );
        if(strlen($file[basename($filename, '.config')])==0) {unset($file[basename($filename, '.config')]);}
      }
    }
  }
  echo " -->\r\n";
  asort($file);
  foreach($file as $filename=>$file_contents) {
    $config=explode("\n", $file_contents);
    foreach($config as $line_number=>$config_option) {
      preg_match_all('`(\w+(=(([0-9+|\w+][\.|/|,|:|-]?)+|[\'|"].*?[\'|"]))?)`', $config_option,  $matches);
      if (count($matches)  > 1) {
        $counter=0;
        foreach ($matches[1] as $option) {
          $counter++;
          $tmp=explode('=', $option);
          $name=$tmp[0];
          $value='';
          if (count($tmp) > 1) {
            if (!empty($tmp[1])) {
              $value = $tmp[1];
              if(substr($value, 0, 1)=="'" and substr($value, -1, 1)=="'") {$value=substr($value, 1, -1);}
            } else {
              $value='';
            }
          }
          if(!isset($configuration[$filename][$line_number][$name])) {
            $configuration[$filename][$line_number][$name]=$value;
          } elseif(!isset($configuration[$filename][$line_number][$name . '_'])) {
            $configuration[$filename][$line_number][$name . '_']=$value;
          }
        }
      }
    }
  }
  if(count($configuration['interface'])>0) { // Done
    foreach($configuration['interface'] as $line) {
      if(isset($line['modify'])) {
        $obj['interface'][$line['name']]=new cInterface($line);
      }
      if(isset($line['add'])) {
        foreach($obj['interface'] as $interface_name=>$interface) {
          if($interface_name==$line['hwdevice']) {
            foreach($line as $key=>$value) {$interface->set($key, $value);}
          }
        }
      }
    }
  }
  if(count($configuration['adminuser'])>0) { // Done
    foreach($configuration['adminuser'] as $line) {
      if(isset($line['add'])) {
        $obj['adminuser'][$line['username']]=new cAdminuser($line);
      }
    }
  }
  if(count($configuration['agent'])>0) { // Done
    foreach($configuration['agent'] as $line) {
      if(isset($line['modify'])) {
        $obj['agent'][$line['name']]=new cAgent($line);
      }
    }
  }
  if(count($configuration['service'])>0) { // Done
    foreach($configuration['service'] as $line) {
      if(isset($line['add']) or isset($line['modify'])) {
        $obj['service'][$line['name']]=new cService($line);
        $obj['service'][$line['name']]->set('agent_object', $obj['agent'][$line['agent']]);
      }
    }
  }
  if(count($configuration['servicegroup'])>0) { // Done
    foreach($configuration['servicegroup'] as $line) {
      if(isset($line['add'])) {
        $obj['servicegroup'][$line['name']]=new cServicegroup($line);
        $members=explode(',', $line['members']);
        if(count($members)>0 and $members[0]!='') {
          foreach($members as $member) {
            if(isset($obj['service'][$member])) {
              $obj['servicegroup'][$line['name']]->add('services', $obj['service'][$member]);
            }
          }
        }
      }
    }
  }
  if(count($configuration['ipsec'])>0) { // Done
    foreach($configuration['ipsec'] as $line) {
      if(isset($line['add'])) {
        $obj['ipsec'][$line['name']]=new cIpsec($line);
      }
    }
  }
  if(count($configuration['burbgroup'])>0) { // Done
    foreach($configuration['burbgroup'] as $line) {
      if(isset($line['add'])) {
        $obj['burbgroup'][$line['name']]=new cBurbgroup($line);
      }
    }
  }
  if(count($configuration['burb'])>0) { // Done
    foreach($configuration['burb'] as $line) {
      if(isset($line['add'])) {
        $obj['burb'][$line['name']]=new cBurb($line);
        $burbgroups=explode(',', $line['burbgroups']);
        if(count($burbgroups)>0 and $burbgroups[0]!='') {
          foreach($burbgroups as $burbgroup) {
            if(isset($obj['burbgroup'][$burbgroup])) {
              $obj['burbgroup'][$burbgroup]->add('burbs', $obj['burb'][$line['name']]);
            }
          }
        }
        if(count($obj['interface'])>0) {
          foreach($obj['interface'] as $interface) {
            if($interface->get('burb')==$line['name']) {$obj['burb'][$line['name']]->add('interface', $interface);}
          }
        }
        if(count($obj['ipsec'])>0) {
          foreach($obj['ipsec'] as $interface) {
            if($interface->get('burb')==$line['name']) {$obj['burb'][$line['name']]->add('interface', $interface);}
          }
        }
      }
    }
  }
  if(count($configuration['host'])>0) {
    foreach($configuration['host'] as $line) {
      if(isset($line['add'])) {
        $obj['host'][$line['name']]=new cHost($line);
      }
    }
  }
  if(count($configuration['ipaddr'])>0) {
    foreach($configuration['ipaddr'] as $line) {
      if(isset($line['add'])) {
        $obj['ipaddr'][$line['name']]=new cIpaddr($line);
      }
    }
  }
  if(count($configuration['subnet'])>0) {
    foreach($configuration['subnet'] as $line) {
      if(isset($line['add'])) {
        $obj['subnet'][$line['name']]=new cSubnet($line);
      }
    }
  }
  if(count($configuration['iprange'])>0) {
    foreach($configuration['iprange'] as $line) {
      if(isset($line['add'])) {
        $obj['iprange'][$line['name']]=new cIprange($line);
      }
    }
  }
  if(count($configuration['geolocation'])>0) {
    foreach($configuration['geolocation'] as $line) {
      if(isset($line['add'])) {
        $obj['geolocation'][$line['name']]=new cGeolocation($line);
      }
    }
  }
  if(count($configuration['netmap'])>0) {
    foreach($configuration['netmap'] as $line) {
      if(isset($line['add'])) {
        $obj['netmap'][$line['name']]=new cNetmap($line);
        $members=explode(',', $line['members']);
        if(count($members)>0 and $members[0]!='') {
          foreach($members as $member) {
            $setting=explode(':', $member);
            if(isset($obj[$setting[0]][$setting[1]]) and isset($obj[$setting[0]][$setting[2]])) {
              $obj['netmap'][$line['name']]->add('members_', array('original'=>$obj[$setting[0]][$setting[1]], 'translated'=>$obj[$setting[0]][$setting[2]]));
            }
          }
        }
      }
    }
  }
  if(count($configuration['netgroup'])>0) {
    foreach($configuration['netgroup'] as $line) {
      if(isset($line['add'])) {
        $obj['netgroup'][$line['name']]=new cNetgroup($line);
        $members=explode(',', $line['members']);
        if(count($members)>0 and $members[0]!='') {
          foreach($members as $member) {
            $setting=explode(':', $member);
            if(isset($obj[$setting[0]][$setting[1]])) {
              $obj['netgroup'][$line['name']]->add('members_', $obj[$setting[0]][$setting[1]]);
            }
          }
        }
      }
    }
  }
  $obj['rulegroup']['__ROOT']=new cRulegroup(array('disable'=>'no'));
  if(count($configuration['policy'])>0) {
    foreach($configuration['policy'] as $line) {
      if($line['table']=='rulegroup') {
        $obj['rulegroup'][$line['name']]=new cRulegroup($line);
      } else {
        $obj['rule'][$line['name']]=new cRule($line);
        foreach(array('dest', 'source', 'service', 'redir', 'nat_addr') as $type) {
          if($line[$type]!='*') {
            $type_object=explode(':', $line[$type]);
            $obj['rule'][$line['name']]->add($type . '_', $obj[$type_object[0]][$type_object[1]]);
          }
        }
        foreach(array('dest_burbs', 'source_burbs') as $type) {
          if($line[$type]!='*') {
            $locations=explode(',', $line[$type]);
            if(count($locations)>0 and $locations[0]!='') {
              foreach($locations as $location) {
                $location_object=explode(':', $line[$type]);
                $obj['rule'][$line['name']]->add($type . '_', $obj[$location_object[0]][$location_object[1]]);
              }
            }
          }
        }
      }
      if($line['rulegroup']=='') {
        $line['rulegroup']='__ROOT';
      }
      if($line['rulegroup']!='') {
        $obj['rulegroup'][$line['rulegroup']]->add('children', $obj[$line['table']][$line['name']]);
      }
    }
  }

echo "<table>";
echo $obj['rulegroup']['__ROOT'];
echo "</table>";
