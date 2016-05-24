<?php

class ParentDao {

  public function getParentsMobileNumbers() {

    try {

      mysql_connect(DB_HOST,DB_USER,DB_PASS);
      mysql_select_db(DB_NAME);
      
      $query = "Select mother_contact_mobile from rc_student_info where mother_contact_mobile != 0";

      $result = mysql_query($query);

      $backups = array();
      $i       = 0;

      if($result)
      {
        while($row = mysql_fetch_array($result))
        {
          $backups[ $i ] = $row[0];
          $i ++;
        }
      }

      $query = "Select father_contact_number_mobile from rc_student_info where father_contact_number_mobile != 0";

      $result = mysql_query($query);

      if($result)
      {
        while($row = mysql_fetch_array($result))
        {
          $backups[ $i ] = $row[0];
          $i ++;
        }
      }

      $backups = array_unique($backups);
      array_push($backups, '773667501');
      array_push($backups, '719109916');
      array_push($backups, '778463291');
      array_push($backups, '712272727');
      array_push($backups, '711739906');

      mysql_close();

      return $backups;

    } catch (Exception $e) {
      throw $e;
    }

  }
}