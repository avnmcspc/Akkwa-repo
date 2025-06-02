<?php
session_start();
// Destroy all session data
session_destroy();
// Send a success response
http_response_code(200);
