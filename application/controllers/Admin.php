<?php date_default_timezone_set("Asia/Bangkok");

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->status == false || !isset($this->session->status))
        {
            redirect(base_url('home'));
        }
    }
    public function dashboard()
    {

        $member = $this->UserModel->get_member(null, 5);

        // Dashboard
        $data = array(
            'title' => 'Dashboard',
            'user' => $this->UserModel->get_user(array('id' => $this->session->id))->row_array(),
            'member' => $member->result(),
            'memberCount' => $member->num_rows()
        );

        // var_dump($data['member']);
        // return;
        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function member()
    {
        $member = $this->UserModel->get_user();

        // Dashboard
        $data = array(
            'title' => 'User',
            'user' => $this->UserModel->get_user(array('id' => $this->session->id))->row_array(),
            'member' => $member->result(),
            'memberCount' => $member->num_rows()
        );

        // var_dump($data);
        // return;
        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/member', $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function tambah_member()
    {
        $date = new DateTime();
        $this->form_validation->set_rules(
            'pwd1',
            'Pwd1',
            'matches[pwd2]',
            array(
                'matches' => 'Password tidak sama'
            )
        );
        $this->form_validation->set_rules(
            'pwd2',
            'Pwd2',
            'matches[pwd1]',
            array(
                'matches' => 'Password tidak sama'
            )
        );
        $this->form_validation->set_rules(
            'email',
            'Email',
            'is_unique[users.email]',
            array(
                'is_unique' => 'Email sudah terdaftar'
            )
        );

        if ($this->form_validation->run() == false)
        {
            redirect('admin/member');
        }
        else
        {

            $pwd = $this->input->post('pwd1');
            $pwd = password_hash($pwd, PASSWORD_DEFAULT);

            $data = array(
                'nama_depan' => trim($this->input->post('namaDep')),
                'nama_belakang' => trim($this->input->post('namaBel')),
                'email' => trim($this->input->post('email')),
                'pass' => $pwd,
                'role' => 2,
                'tgl_edit' => $date->getTimestamp(),
                'tgl_buat' => $date->getTimestamp()
            );

            $run = $this->UserModel->add_user($data);

            if ($run)
            {
                $this->session->set_flashdata('pesan', 'Data berhasil ditambahkan!');
                redirect('admin/member');
            }
            else
            {
                echo var_dump($data);
            }
        }
    }

    public function edit_member($param)
    {
        $member = $this->UserModel->get_member($param);
        
        // Member Edit
        $data = array(
            'title' => 'Edit Member',
            'user' => $this->UserModel->get_user(array('id' => $this->session->id))->row(),
            'member' => $member->row(),
        );


        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/editMember', $data);
        $this->load->view('admin/template/footer', $data);
    }

    public function update_member()
    {
        $p = $this->input->post('pass_baru');
        $p_n = $this->input->post('pass_baru2');

        $data = array(
            'nama_depan' => $this->input->post('namaDep'),
            'nama_belakang' => $this->input->post('namaBel'),
            'email' => $this->input->post('email'),
        );

        if ($p != null && $p_n != null)
        {
            if ($p == $p_n)
            {

                $pwd_n = array(
                    'pass' => password_hash($this->input->post('pass_baru'), PASSWORD_DEFAULT)
                );
                $this->UserModel->set_user($pwd_n, array('id_user' => $this->input->post('id')));
                echo 'diupdate pass';
            }
        } else {
            $this->UserModel->set_user($data, array('id_user' => $this->input->post('id')));
        }
        redirect('admin/member');
    
    }

    public function hapus_member($param)
    {
        $run = $this->UserModel->del_user(array('id' => $param));
        if ($run)
        {
            $this->session->set_flashdata('pesan', 'Data Berhasil Dihapus!');
            redirect('admin/member');
        }
        else
        {
            var_dump($run);
        }
    }

    public function produk()
    {
        // $member = $this->UserModel->get_user();

        // Dashboard
        $data = array(
            'title' => 'Produk',
            'produk' => $this->ProdukModel->get_produk()->result()
        );

        // var_dump($data);
        // return;
        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/produk', $data);
        $this->load->view('admin/template/footer', $data);
    }
}
