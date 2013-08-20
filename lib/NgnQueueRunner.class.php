<?php

if (!defined('NGN_ENV_PATH')) throw new NgnException('NGN_ENV_PATH not defined');

class NgnQueueRunner {

  /**
   * @var Pheanstalk
   */
  public $oP;
  
  public function __construct() {
    $this->oP = NgnQueueCore::getPheanstalk();
  }

  public function run() {
  	LogWriter::str('queueRunner', 'start watch');
    $job = $this->oP->watch(NgnQueueCore::TUBE_NAME)->ignore('default')->reserve();
    if ($job) {
      LogWriter::str('queueRunner', 'new job ID='.$job->getId());
      $this->runJob($job);
      return true;
    }
    return false;
  }
  
  protected function runJob(Pheanstalk_Job $job) {
    $data = $job->getData();
    $id = $job->getId();
    $this->oP->delete($job);
    if (!Arr::checkEmpty($data, ['projectName', 'projectKey'], true)) {
      LogWriter::str('queueRunner', 'something wrong. data: '.getPrr($data));
      Arr::checkEmpty($data, ['projectName', 'projectKey']);
      //return;
    }
    LogWriter::str('queueRunner', 'run job ID='.$id.' on project '.$data['projectName']);
    QueueMem::setProjectKey($data['projectKey']);
    QueueMem::setIfNotExists($id, $data);
    // ------------ run external program to lunch job in project enviroment ----------------
    output("\n");
    sys('sudo -u www-data '.
      NGN_ENV_PATH."/run/site.php {$data['projectName']} runQueueJob ".$id, true);
    return true;
  }

}