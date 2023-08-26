<?php
  /**
   * Sends data to the system log - json-encoded if not a string.
   * @param array|string $data Data details in array or in text.
   */
  function ErrorLog($data) {
    if (is_string($data)) {
      error_log($data);
      return;
    }
    error_log(json_encode($data));
  }
?>
