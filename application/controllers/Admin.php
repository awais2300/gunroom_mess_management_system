<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->session->has_userdata('user_id')) {
            $id = $this->session->userdata('user_id');

            $this->load->view('Admin/admin');
        } else {
            $this->load->view('Admin/login');
        }
    }

    public function add_users()
    {
        $this->load->view('Admin/create_user');
    }

    public function multiselect()
    {
        $this->load->view('multiselect');
    
    }
    public function login_process()
    {
        if ($this->input->post()) {
            $postedData = $this->security->xss_clean($this->input->post());
            $username = $postedData['username'];
            $password = $postedData['password'];
            $query = $this->db->where('username', $username)->where('acct_type', 'admin')->get('security_info')->row_array();
            $hash = $query['password'];

            if (!empty($query)) {
                if (password_verify($password, $hash)) {
                    $this->session->set_userdata('user_id', $query['id']);
                    $this->session->set_userdata('status', $query['type']);
                    $this->session->set_userdata('username', $query['username']);
                    $this->session->set_flashdata('success', 'Login successfully');
                    redirect('Admin');
                } else {
                    $this->session->set_flashdata('failure', 'No such user exist. Kindly create New User using Admin panel');
                    redirect('Admin');
                }
                //print_r($query); exit; 
            } else {
                $this->session->set_flashdata('failure', 'Login failed');
                redirect('Admin');
            }
        }
    }
    public function update_complaint_process()
    {
        $postData = $this->security->xss_clean($this->input->post());

        $name = $postData['name'];
        $p_no = $postData['p_no'];
        $date = $postData['date'];
        $allocated_to = $postData['allocated_to'];
        $type = $postData['type'];
        $location = $postData['location'];
        $description= $postData['description'];
        $remarks=$postData['remarks'];
        $old_file=$postData['old_file'];
        $complaint_id=$postData['complaint_id'];
        // echo $_FILES['attachement'];exit;
        //$upload1 = $this->upload_attachement($_FILES['attachement']);
        if ($_FILES['attachement']['name'][0] != NULL) {
            $upload1 = $this->upload_attachement($_FILES['attachement']);
            if (count($upload1) > 1) {
                $attachement = implode(',', $upload1);
            } else {
                $attachement = $upload1[0];
            }
        } else {
            $attachement =  $old_file;
        }
       
                $insert_array = array(
                    'name' => $name,
                    'p_no' => $p_no,
                    'description' => $description,
                    'date' =>$date,
                    'allocated_to' => $allocated_to,
                    'type'=> $type,
                    'attachement' => $attachement,
                    'location'=>$location,
                    'remarks'=>$remarks,
                    'seen'=>'no',
                    'admin_seen'=>'no'
                );
                //print_r($insert_array);exit;
                $this->db->where('id',$complaint_id);
                $insert = $this->db->update('complaints', $insert_array);
     

        if (!empty($insert)) {
            $this->session->set_flashdata('success', 'Remarks added successfully');
            redirect('Admin/complaint');
        } else {
            $this->session->set_flashdata('failure', 'Something went wrong, try again.');
            redirect('Admin/update_complaint');
        }
    }
    public function  update_guest_reservation_process($id=null)
    {
        $postData = $this->security->xss_clean($this->input->post());

        $name = $postData['name'];
        $p_no = $postData['p_no'];
        $date = $postData['date'];
        $total_guests = $postData['total_guests'];
        $menu = $postData['menu'];
        $remarks=$postData['remarks'];
       //print_r($menu);
       $muenu_items=implode(',',$menu);
      // print_r($muenu_items);exit;
        $description = $postData['description'];
        // echo $_FILES['attachement'];exit;
        //$upload1 = $this->upload_attachement($_FILES['attachement']);
       

        $insert_array = array(
            'name' => $name,
            'p_no' => $p_no,
            'description' => $description,
            'date' => $date,
            'total_guests' => $total_guests,
            'menu' => $muenu_items,
            'seen'=>'no',
            'admin_seen'=>'no',
            'remarks'=>$remarks
        );
        //print_r($insert_array);exit;
        $this->db->where('id',$id);
        $insert = $this->db->update('guest_reservation', $insert_array);


        if (!empty($insert)) {
            $this->session->set_flashdata('success', 'Submitted successfully');
            redirect('Admin/reservation');
        } else {
            $this->session->set_flashdata('failure', 'Something went wrong, try again.');
            redirect('Admin/update_guest_reservation');
        }
    }

    public function  update_requesting_menu_process($id=null)
    {
        $postData = $this->security->xss_clean($this->input->post());

        $name = $postData['name'];
        $p_no = $postData['p_no'];
        $date = $postData['date'];
        $no_of_persons = $postData['no_of_persons'];
        $menu = $postData['menu'];
        $description = $postData['description'];
        $remarks=$postData['remarks'];

        $muenu_items=implode(',',$menu);
        // echo $_FILES['attachement'];exit;
        //$upload1 = $this->upload_attachement($_FILES['attachement']);

        $insert_array = array(
            'name' => $name,
            'p_no' => $p_no,
            'description' => $description,
            'date' => $date,
            'total_persons' => $no_of_persons,
            'menu' => $muenu_items,
            'seen'=>'no',
            'admin_seen'=>'no',
            'remarks'=>$remarks
        );
        //print_r($insert_array);exit;
        $this->db->where('id',$id);
        $insert = $this->db->insert('requesting_menu', $insert_array);


        if (!empty($insert)) {
            $this->session->set_flashdata('success', 'Submitted successfully');
            redirect('Admin/menu_requests');
        } else {
            $this->session->set_flashdata('failure', 'Something went wrong, try again.');
            redirect('Admin/update_menu_requests');
        }

    }
    public function reservation()
    {
        if ($this->session->has_userdata('user_id')) {
            $data['reservation_data'] = $this->db->get('guest_reservation')->result_array();
            $query = $this->db->set('admin_seen', 'yes')->where('admin_seen', 'no')->update('guest_reservation');
            $this->load->view('Admin/reservations',$data);
        }
    } public function menu_requests()
    {
        if ($this->session->has_userdata('user_id')) {
            $data['menu_request_data'] = $this->db->get('requesting_menu')->result_array();
            $query = $this->db->set('admin_seen', 'yes')->where('admin_seen', 'no')->update('requesting_menu');
            $this->load->view('Admin/menu_requests',$data);
        }
    }
    public function update_guest_reservation($id=null){
        $data['update_guest_reservation_data'] = $this->db->where('id',$id)->get('guest_reservation')->row_array();
        $data['menu_data'] = $this->db->where('status','Available')->get('mess_menu')->result_array();
        $this->load->view('Admin/update_guest_reservation',$data);
    }

    public function update_menu_requests($id=null){
        $data['update_menu_requests_data'] = $this->db->where('id',$id)->get('requesting_menu')->row_array();
        $data['menu_data'] = $this->db->where('status','Available')->get('mess_menu')->result_array();
        $this->load->view('Admin/update_requesting_menu',$data);
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('Admin');
    }

    public function add_user()
    {
        if ($this->input->post()) {
            $postData = $this->security->xss_clean($this->input->post());

            $username = $postData['username'];
            $password = password_hash($postData['password'], PASSWORD_DEFAULT);
            $status = $postData['status'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $name = $_POST['name'];
            $acct_type=$_POST['acct_type'];

            $insert_array = array(
                'username' => $username,
                'password' => $password,
                'acct_type' => $acct_type,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'full_name' => $name
            );

            $insert = $this->db->insert('security_info', $insert_array);

            if (!empty($insert)) {
                $this->session->set_flashdata('success', 'Data Submitted successfully');
                redirect('Admin/add_users');
            } else {
                $this->session->set_flashdata('failure', 'Something went wrong, try again.');
                redirect('Admin/add_users');
            }
        } else {
            $this->session->set_flashdata('failure', 'Something went wrong, Try again.');
            redirect('Admin/add_users');
        }
    }
    public function complaint(){
        if ($this->session->has_userdata('user_id')) {
            $data['complaint_data'] = $this->db->get('complaints')->result_array();
            $query=$this->db->set('admin_seen','yes')->where('admin_seen','no')->update('complaints');
            if($query){
            $this->load->view('Admin/complaint',$data);
            }
        }
    }
    public function update_complaint($complaint_id=null){
        if ($this->session->has_userdata('user_id')) {
            $data['complaint_data'] = $this->db->where('id',$complaint_id)->get('complaints')->row_array();
            $this->load->view('Admin/update_complaint',$data);
        }
    }
    
    public function view_activity_log()
    {
        if ($this->session->has_userdata('user_id')) {
            $data['activity_log'] = $this->db->get('activity_log')->result_array();
            $this->load->view('Admin/activity_log', $data);
        }
    }
}
