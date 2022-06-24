<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');
include 'connection/mysqli_connection.php';

include('models/email.php');
include('models/phone.php');
include('models/branch.php');
include('models/customerGroup.php');
include('models/contact.php');
include('models/customer.php');

$sql = "SELECT c.id, c.name, c.referenceId, c.isActive, b.id branchId, b.name branch, cg.id customerGroupId, cg.name customerGroup, cg.orderNo orderNo, ct.id contactId, ct.name contact,ct.gender, 'Parent' relationship, 'Work' emailType, ct.email, 'Mobile' phoneType, '+251' dialingCode, ct.phone_1 phone, ct.email primaryEmail, ct.phone_2 primaryPhone
FROM customer c
    INNER JOIN branch b ON c.branchId = b.id
    INNER JOIN customergroup cg ON c.customerGroupId  = cg.id
    INNER JOIN contact ct ON c.contactId  = ct.id
ORDER BY c.id";

$result = mysqli_query($con, $sql);
$rows = mysqli_num_rows($result);

$pageSize = 10;
$pages = ceil($rows / $pageSize);

// the current page number
if (!isset($_GET['page']) || !isset($_GET['pageSize'])) {
  $page = 1;
  $pageSize = 10;
}
else {
  $page = $_GET['page'];
  $pageSize = $_GET['pageSize'];
}

// LIMIT starting number for the records on the current page
$this_page_first_result = ($page - 1) * $pageSize;

$sql = "SELECT c.id, c.name, c.referenceId, c.isActive, b.id branchId, b.name branch, cg.id customerGroupId, cg.name customerGroup, cg.orderNo orderNo, ct.id contactId, ct.name contact,ct.gender, 'Parent' relationship, 'Work' emailType, ct.email, 'Mobile' phoneType, '+251' dialingCode, ct.phone_1 phone, ct.email primaryEmail, ct.phone_2 primaryPhone
FROM customer c
    INNER JOIN branch b ON c.branchId = b.id
    INNER JOIN customergroup cg ON c.customerGroupId  = cg.id
    INNER JOIN contact ct ON c.contactId  = ct.id
ORDER BY c.id LIMIT " . $this_page_first_result . "," . $pageSize;

$result = mysqli_query($con, $sql);
$i = 0;
$response = array();

$status_code = "404";
$error_message = "Data not found";

if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_array($result)) {

    if ($row['isActive'] == "1") {
      $isActive = true;
    }
    else {
      $isActive = false;
    }

    if ($row['gender'] = "null") {
      $gender = 'Male';
    }
    else {
      $gender = $row['gender'];
    }

    $branch = new Branch();
    $branch->id = $row['branchId'];
    $branch->name = ucwords($row['branch']);

    $customerGroup = new CustomerGroup();
    $customerGroup->id = $row['customerGroupId'];
    $customerGroup->name = ucwords($row['customerGroup']);
    $customerGroup->orderNo = $row['orderNo'];

    $email = new Email();
    $email->label = $row['emailType'];
    $email->email = $row['email'];

    $phone = new Phone();
    $phone->label = $row['phoneType'];
    $phone->dialingCode = $row['dialingCode'];
    $phone->phone = $row['phone'];

    $contact = new Contact();
    $contact->id = $row['contactId'];
    $contact->name = ucwords($row['contact']);
    $contact->gender = $gender;
    $contact->relationship = $row['relationship'];

    $contact->emails = array($email);
    $contact->phoneNumbers = array($phone);
    $contact->primaryEmail = $row['primaryEmail'];
    $contact->primaryPhone = $row['primaryPhone'];

    $customer = new Customer();
    $customer->id = strtoupper($row['referenceId']);
    $customer->name = ucwords($row['name']);
    $customer->isActive = $isActive;
    $customer->branch = $branch;
    $customer->customerGroup = $customerGroup;
    $customer->contacts = array($contact);

    $response['page'] = $page;
    $response['pages'] = $pages;
    $response['pageSize'] = $pageSize;
    $response['rows'] = $rows;
    $response['data'][$i] = $customer;

    $i++;
  }
  echo json_encode($response, JSON_PRETTY_PRINT);
}
else {

  echo json_encode(array('status' => $status_code, 'message' => $error_message));
}

?>