<?php

class StatController extends AppController {

    var $name = 'Stat';
    var $uses = array('UserFanli', 'StatJump', 'UserCandidate', 'Alert');


    function basic(){
        //当前配置状态
        $config = C('config');
        $this->set('c', $config);
        
        //昨日、今日关键数据
        $s = array();
        $yesterday = date('Y-m-d', time() - 24*3600);
        $today = date('Y-m-d');
        $s['y_reg_num'] = $this->UserFanli->findCount("created>'".$yesterday."' AND created<'".$today."'");
        $s['t_reg_num'] = $this->UserFanli->findCount("created>'".$today."'");
        
        $s['y_jump_num'] = $this->StatJump->findCount("ts>'".$yesterday."' AND ts<'".$today."' AND outcode<>'test'");
        $s['t_jump_num'] = $this->StatJump->findCount("ts>'".$today."' AND outcode<>'test'");
        
        $s['y_price_num'] = $this->StatJump->findSum('p_price', "ts>'".$yesterday."' AND ts<'".$today."'");
        $s['t_price_num'] = $this->StatJump->findSum('p_price', "ts>'".$today."'");
        
        $s['y_fanli_num'] = $this->StatJump->findSum('p_fanli', "ts>'".$yesterday."' AND ts<'".$today."'");
        $s['t_fanli_num'] = $this->StatJump->findSum('p_fanli', "ts>'".$today."'");
        
        $s['y_r_num'] = $this->UserFanli->findCount("ts>'".$yesterday."' AND ts<'".$today."' AND status=2 AND role=3");
        $s['t_r_num'] = $this->UserFanli->findCount("ts>'".$today."' AND status=2 AND role=3");
        
        $s['total_cash'] = $this->UserFanli->findSum('fl_cash');
        $s['total_fb'] = $this->UserFanli->findSum('fl_fb');
        
        $this->set('s', $s);
        
        //跳转中介统计
        $j1 = $this->StatJump->query("SELECT count(*) as nu, jumper_uid, username, a.area, sum(p_price) price, sum(p_fanli) fanli FROM stat_jump a LEFT JOIN user_fanli ON(jumper_uid = userid) WHERE a.ts>'".$yesterday."' AND a.ts<'".$today."' AND outcode<>'test' GROUP BY jumper_uid ORDER BY nu DESC");
        $j2 = $this->StatJump->query("SELECT count(*) as nu, jumper_uid, username, a.area, sum(p_price) price, sum(p_fanli) fanli FROM stat_jump a LEFT JOIN user_fanli ON(jumper_uid = userid) WHERE a.ts>'".$today."' AND outcode<>'test'  GROUP BY jumper_uid ORDER BY nu DESC");
        clearTableName($j1);
        clearTableName($j2);
        $this->set('js1', $j1);
        $this->set('js2', $j2);
        
        
        //最新跳转记录
        $last_jumps = $this->StatJump->findAll('', '', 'id DESC', 15);
        clearTableName($last_jumps);
        $this->set('last_jumps', $last_jumps);
        
        //最新报警
        $last_alerts = $this->Alert->findAll('', '', 'id DESC', 5);
        clearTableName($last_alerts);
        $this->set('last_alerts', $last_alerts);
        
        //特殊账号跳转额
        $sp = @file_get_contents('/tmp/overlimit_day/SP_FANLI_MAX/' . date('Ym'));
        $this->set('sp', $sp);
        
    }
    
    function jump($date = null){
        if(!$date)$date = date('Y-m-d');
        
        $tomo = date('Y-m-d', strtotime($date)+24*3600);
        //最新跳转记录
        $last_jumps = $this->StatJump->findAll("ts>'{$date}' AND ts<'{$tomo}' AND outcode<>'test'", '', 'id DESC');
        clearTableName($last_jumps);
        $this->set('last_jumps', $last_jumps);
        $this->set('date', $date);
    }

}

?>