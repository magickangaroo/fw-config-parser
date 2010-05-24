<?php
// Sidewinder Config Parser - Written by JonTheNiceGuy
// Patches supplied by PHPCodeMonkey

  class cGeneric {
    protected $data=array();
    function __construct($arr) {$this->data=$arr;}
    function add($key='', $obj) {$this->data[$key][]=$obj;}
    function get($key='') {if(isset($this->data[$key])) {return $this->data[$key];} else {return(null);}}
    function set($key='', $data=null) {
      if(isset($this->data[$key])) {
        if(!empty($data)) {
          $this->data[$key]=$data;
        } else {
          unset($this->data[$key]);
        }
      } else {
        $this->data[$key]=$data;
      }
    }
  }
  class cInterface extends cGeneric { // Done
    function __toString() {
      if($this->data['hwdevice']==$this->data['name'] or $this->data['hwdevice']=='') {
        return "'" . $this->data['name'] . "'";
      } else {
        return("'" . $this->data['name'] . "' (" . $this->data['hwdevice'] . ')');
      }
    }
  }
  class cAdminuser extends cGeneric { // Done
    function __toString() {return "User: '" . $this->data['username'] . "'";}
  }
  class cBurb extends cGeneric { // Done
    function __toString() {
      $not_first=0;
      $return="'" . $this->data['name'] . "'";
      if(isset($this->data['interface'])) {
        $return.=' (';
        foreach($this->data['interface'] as $interface) {
          if($not_first==1) {$return.=', ';}
          $not_first=1;
          $return.=$interface;
        }
        $return.=')';
      }
      return($return);
    }
  }
  class cBurbgroup extends cGeneric { // Done
    function __toString() {
      $not_first=0;
      $return="'" . $this->data['name'] . "'";
      if(isset($this->data['burbs'])) {
        $return.=' (';
        foreach($this->data['burbs'] as $burb) {
          if($not_first==1) {$return.=', ';}
          $not_first=1;
          $return.=$burb;
        }
        $return.=')';
      }
      return($return);
    }
  }
  class cAgent extends cGeneric { // Done
    function __toString() {return ucfirst($this->data['type']) . ": '" . $this->data['name'] . "'";}
  }
  class cIpsec extends cGeneric { // Done
    function __toString() {return "'" . $this->data['name'] . "' (" . ucfirst($this->data['encapsulation']) . ' to ' . $this->data['remotegw'] . ")";}
  }
  class cService extends cGeneric { // Done
    function __toString() {
      $not_first=0;
      $return=$this->data['agent_object'] . " - '" . $this->data['name'] . "'";
      if(isset($this->data['tcp_ports']) and $this->data['tcp_ports']!='') {
        $return.=' (TCP: ' . $this->data['tcp_ports'] . ')';
      }
      if(isset($this->data['udp_ports']) and $this->data['udp_ports']!='') {
        $return.=' (UDP: ' . $this->data['udp_ports'] . ')';
      }
      return($return);
    }
  }
  class cServicegroup extends cGeneric { // Done
    function __toString() {
      $not_first=0;
      $return="'" . $this->data['name'] . "'";
      if(isset($this->data['services'])) {
        $return.=' (';
        foreach($this->data['services'] as $service) {
          if($not_first==1) {$return.=', ';}
          $not_first=1;
          $return.=$service;
        }
        $return.=')';
      }
      return($return);
    }
  }
  class cHost extends cGeneric {
    function __toString() {return "'" . $this->data['name'] . "' (" . $this->data['host_'] . ')';}
  }
  class cIpaddr extends cGeneric {
    function __toString() {return "'" . $this->data['name'] . "' (" . $this->data['ipaddr_'] . ')';}
  }
  class cSubnet extends cGeneric {
    function __toString() {return "'" . $this->data['name'] . "' (" . $this->data['subnet_'] . '/' . $this->data['bits'] . ')';}
  }
  class cIprange extends cGeneric {
    function __toString() {return "'" . $this->data['name'] . "' (" . $this->data['begin'] . '-' . $this->data['end'] . ')';}
  }
  class cGeolocation extends cGeneric {
    function __toString() {
      $not_first=0;
      $return="'" . $this->data['name'] . "'";
      if(isset($this->data['members'])) {
        $return.=' (' . $this->data['members'] . ')';
      }
      return($return);
    }
  }
  class cNetmap extends cGeneric {
    function __toString() {
      $not_first=0;
      $return="'" . $this->data['name'] . "'";
      if(isset($this->data['members_'])) {
        $return.=' (';
        foreach($this->data['members_'] as $member) {
          if($not_first==1) {$return.=', ';}
          $not_first=1;
          $return.=$member['original'] . " -> " . $member['translated'];
        }
        $return.=')';
      }
      return($return);
    }
  }
  class cNetgroup extends cGeneric {
    function __toString() {
      $not_first=0;
      $return="'" . $this->data['name'] . "'";
      if(isset($this->data['members_'])) {
        $return.=' (';
        foreach($this->data['members_'] as $member) {
          if($not_first==1) {$return.=', ';}
          $not_first=1;
          $return.=$member;
        }
        $return.=')';
      }
      return($return);
    }
  }
  class cRulegroup extends cGeneric {
    function __toString() {
      if($this->data['disable']=='no') {
        $state='';
      } else {
        $state='disabled';
      }
      if(!isset($this->data['name'])) {$this->data['name']='';}
      $return="<tr class='{$state} rulegroup'><td class='rulegroup' colspan='9'>{$this->data['name']}</td></tr>";
      if(isset($this->data['children']) and count($this->data['children'])>0) {
        $return.="\r\n<tbody class='child_policy {$state}'>\r\n";
        foreach($this->data['children'] as $child) {$return.=$child;}
        $return.="\r\n</tbody>\r\n";
      }
      return($return);
    }
  }
  class cRule extends cGeneric {
    function __toString() {
      if($this->data['disable']=='no') {
        $return="<tr class='enabled'>";
      } else {
        $return="<tr class='disabled'>";
      }
      $return.="\r\n<td class='name'>{$this->data['name']}</td>";
      if($this->data['source_burbs']!='*') {
        $return.="\r\n<td class='source_burbs'>";
        $not_first=0;
        if(count($this->data['source_burbs_'])>0) {
          foreach($this->data['source_burbs_'] as $source_burb) {
            if($not_first==1) {$return.=", ";}
            $return.=$source_burb;
            $not_first=1;
          }
        } else {
          $return.=$this->data['source_burbs'];
        }
        $return.="</td>";
      } else {
        $return.="\r\n<td class='source_burbs'>Any</td>";
      }
      if($this->data['source']!='*') {
        $return.="\r\n<td class='source'>";
        $not_first=0;
        foreach($this->data['source_'] as $source) {
          if($not_first==1) {$return.=", ";}
          $return.=$source;
          $not_first=1;
        }
        $return.="\r\n</td>";
      } else {
        $return.="\r\n<td class='source'>Any</td>";
      }
      if($this->data['nat_addr']!='') {
        $return.="\r\n<td class='source_nat'>";
        $not_first=0;
        foreach($this->data['nat_addr_'] as $source) {
          if($not_first==1) {$return.=", ";}
          $return.=$source;
          $not_first=1;
        }
        $return.="\r\n</td>";
      } else {
        $return.="\r\n<td class='source_nat'>&nbsp;</td>";
      }
      if($this->data['dest_burbs']!='*') {
        $return.="\r\n<td class='dest_burbs'>";
        $not_first=0;
        if(count($this->data['dest_burbs_'])>0) {
          foreach($this->data['dest_burbs_'] as $dest_burbs) {
            if($not_first==1) {$return.=", ";}
            $return.=$dest_burbs;
            $not_first=1;
          }
        } else {
          $return.=$this->data['dest_burbs'];
        }
        $return.="\r\n</td>";
      } else {
        $return.="\r\n<td class='dest_burbs'>Any</td>";
      }
      if($this->data['dest']!='*') {
        $return.="\r\n<td class='dest'>";
        $not_first=0;
        foreach($this->data['dest_'] as $dest) {
          if($not_first==1) {$return.=", ";}
          $return.=$dest;
          $not_first=1;
        }
        $return.="\r\n</td>";
      } else {
        $return.="\r\n<td class='dest'>Any</td>";
      }
      if($this->data['redir']!='') {
        $return.="\r\n<td class='dest_nat'>";
        $not_first=0;
        foreach($this->data['redir_'] as $redir) {
          if($not_first==1) {$return.=", ";}
          $return.=$redir;
          $not_first=1;
        }
        $return.="\r\n</td>";
      } else {
        $return.="\r\n<td class='dest_nat'>&nbsp;</td>";
      }
      if($this->data['service']!='*') {
        $return.="\r\n<td class='service'>";
        $not_first=0;
        foreach($this->data['service_'] as $service) {
          if($not_first==1) {$return.=", ";}
          $return.=$service;
          $not_first=1;
        }
        $return.="\r\n</td>";
      } else {
        $return.="\r\n<td class='service'>Any</td>";
      }
      if($this->data['redir_port']!='') {
        $return.="\r\n<td class='service_nat'>";
        $not_first=0;
        if(isset($this->data['redir_port_']) and count($this->data['redir_port_'])>0) {
          foreach($this->data['redir_port_'] as $redir_port) {
            if($not_first==1) {$return.=", ";}
            $return.=$redir_port;
            $not_first=1;
          }
        } else {
          $return.=$this->data['redir_port'];
        }
        $return.="\r\n</td>";
      } else {
        $return.="\r\n<td class='service_nat'>&nbsp;</td>";
      }
      $return.="</tr>";
      return($return);
    }
  }
