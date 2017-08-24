<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin_model');
		$this->load->model('auth_model');
		$this->load->model('siswa_model');
	}

	public function index()
	{
		$id = $this->session->userdata('PEMBIMBING_ID');
		$data['siswa'] = $this->admin_model->get_data_siswa();
		$data['total'] = $this->admin_model->total_records();
		$data['total_v'] = $this->admin_model->total_verified();
		$data['total_u'] = $this->admin_model->total_unverified();
		$data['total_p'] = $this->admin_model->total_petugas();	
		$data['petugas'] = $this->admin_model->get_pembimbing($id);	
		$data['main_view'] = 'dashboard_view';
		$this->load->view('template', $data);
	}

	public function dashboard()
	{
		if($this->session->userdata('logged_in') == TRUE){
			if($this->session->userdata('ROLE') == 'Admin'){
				$id = $this->session->userdata('PEMBIMBING_ID');
				$data['siswa'] = $this->admin_model->get_data_siswa();
				$data['total'] = $this->admin_model->total_records();
				$data['total_v'] = $this->admin_model->total_verified();
				$data['total_u'] = $this->admin_model->total_unverified();
				$data['total_p'] = $this->admin_model->total_petugas();
				$data['petugas'] = $this->admin_model->get_pembimbing($id);	
				$data['main_view'] = 'dashboard_view';
				$this->load->view('template', $data);
			} else {
				redirect('siswa/profile');
			}
		} else {
			redirect('auth');
		}
	}

	public function data_siswa()
	{
		$id = $this->session->userdata('PEMBIMBING_ID');
		$data['petugas'] = $this->admin_model->get_pembimbing($id);	
		$data['siswa'] = $this->admin_model->get_data_siswa();
		$data['main_view'] = 'table_siswa_view';
		$this->load->view('template', $data);
	}

	public function verified($id)
	{
		if($this->admin_model->verified($id) == TRUE){
			$this->session->set_flashdata('success', 'Approval Success');
			redirect('admin/dashboard');
		} else {
			$this->session->set_flashdata('failed', 'Approval Failed');
            redirect('admin/dashboard');
		}
	}

	public function unverified($id)
	{
		if($this->admin_model->unverified($id) == TRUE){
			$this->session->set_flashdata('success', 'Delete Success');
			redirect('admin/dashboard');
		} else {
			$this->session->set_flashdata('failed', 'Delete Failed');
			redirect('admin/dashboard');
		}
	}

	public function del_kegiatan_dashboard($id)
	{
		if($this->siswa_model->del_kegiatan($id) == TRUE){
			$this->session->set_flashdata('success', 'Kegiatan Berhasil Dihapus');
			redirect('admin/data_kegiatan');
		} else {
			$this->session->set_flashdata('failed', 'Kegiatan Gagal Dihapus');
			redirect('admin/data_kegiatan');
		}
	}

	public function data_kegiatan()
	{
		$id = $this->session->userdata('PEMBIMBING_ID');
		$data['petugas'] = $this->admin_model->get_pembimbing($id);	
		$data['kegiatan'] = $this->admin_model->all_kegiatan();
		$data['main_view'] = 'kegiatan_view';
		$this->load->view('template', $data);
	}

	public function add_siswa()
	{	
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['max_size']  = '2000';
		$this->load->library('upload', $config);

            if($this->upload->do_upload('identitas')){
				if($this->admin_model->add_siswa($this->upload->data()) == TRUE){
					$this->session->set_flashdata('success', 'Pendaftaran Berhasil');
		            redirect('admin/data_siswa');
				} else {
					$this->session->set_flashdata('failed', 'Pendaftaran Gagal');
		            redirect('admin/data_siswa');
				}
			} else {
				$this->session->set_flashdata('failed', $this->upload->display_errors());
		        redirect('admin/data_siswa');
			}
	}

	public function edit_siswa($id)
	{
		$id = $this->session->userdata('PEMBIMBING_ID');
		$data['petugas'] = $this->admin_model->get_pembimbing($id);	
		$data['siswa'] = $this->admin_model->get_detail_siswa($id);
		$data['main_view'] = 'edit_siswa_view';
		$this->load->view('template', $data);
	}

	public function del_siswa($id)
	{
		if($this->admin_model->del_siswa($id) == TRUE){
			$this->session->set_flashdata('success', 'Siswa Berhasil Dihapus');
			redirect('admin/data_siswa');
		} else {
			$this->session->set_flashdata('failed', 'Siswa Gagal Dihapus');
			redirect('admin/data_siswa');
		}
	}

	public function edit_siswa_submit($id)
	{
		if(!isset($_FILES['identitas']) || $_FILES['identitas']['error'] == UPLOAD_ERR_NO_FILE) {
		    if($this->admin_model->edit_siswa($id) == TRUE){
				$this->session->set_flashdata('success', 'Edit data berhasil');
				redirect('admin/data_siswa');
			} else {
				$this->session->set_flashdata('failed', 'Edit data gagal');
			    redirect('admin/data_siswa');
			}
		} else {
		    $config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['max_size']  = '2000';
			$this->load->library('upload', $config);

			if($this->upload->do_upload('identitas')){
				if($this->admin_model->edit_siswa_upload($id, $this->upload->data()) == TRUE){
					$this->session->set_flashdata('success', 'Edit data berhasil');
					redirect('admin/data_siswa');
				} else {
					$this->session->set_flashdata('failed', 'Edit data gagal');
		            redirect('admin/data_siswa');
				}
			} else {
				$this->session->set_flashdata('failed', $this->upload->display_errors());
		        redirect('admin/data_siswa');
			}
		}
	}

	public function data_admin()
	{
		$id = $this->session->userdata('PEMBIMBING_ID');
		$data['petugas'] = $this->admin_model->get_pembimbing($id);	
		$data['admin'] = $this->admin_model->get_data_admin();
		$data['main_view'] = 'table_admin_view';
		$this->load->view('template', $data);
	}

	public function add_admin()
	{
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['max_size']  = '2000';
		$this->load->library('upload', $config);

        if($this->upload->do_upload('identitas')){
			if($this->admin_model->tambah_petugas($this->upload->data()) == TRUE){
				$this->session->set_flashdata('success', 'Tambah admin berhasil');
				redirect('admin/data_admin');
			} else {
				$this->session->set_flashdata('failed', 'Tambah admin Gagal');
				redirect('admin/data_admin');
			}
		} else {
			$this->session->set_flashdata('failed', $this->upload->display_errors());
	        redirect('admin/data_admin');
		}
	}

	public function edit_petugas($id)
	{
		$id = $this->session->userdata('PEMBIMBING_ID');
		$data['petugas'] = $this->admin_model->get_pembimbing($id);	
		$data['petugas'] = $this->admin_model->get_detail_petugas($id);
		$data['main_view'] = 'edit_admin_view';
		$this->load->view('template', $data);
	}

	public function edit_petugas_submit($id)
	{
		if(!isset($_FILES['identitas']) || $_FILES['identitas']['error'] == UPLOAD_ERR_NO_FILE) {
			if($this->admin_model->edit_petugas($id) == TRUE){
				$this->session->set_flashdata('success', 'Edit data berhasil');
				redirect('admin/data_admin');
			} else {
				$this->session->set_flashdata('failed', 'Edit data gagal');
			    redirect('admin/data_admin');
			}
		} else {
			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['max_size']  = '2000';
			$this->load->library('upload', $config);

	        if($this->upload->do_upload('identitas')){
				if($this->admin_model->edit_petugas_upload($id, $this->upload->data()) == TRUE){
					$this->session->set_flashdata('success', 'Edit data berhasil');
					redirect('admin/data_admin');
				} else {
					$this->session->set_flashdata('failed', 'Edit data gagal');
			        redirect('admin/data_admin');
				}
			} else {
				$this->session->set_flashdata('failed', $this->upload->display_errors());
		        redirect('admin/data_admin');
			}   
		}
	}

	public function del_petugas($id)
	{
		if($this->admin_model->del_petugas($id) == TRUE){
			$this->session->set_flashdata('success', 'Petugas Berhasil Dihapus');
			redirect('admin/data_admin');
		} else {
			$this->session->set_flashdata('failed', 'Siswa Gagal Dihapus');
			redirect('admin/data_admin');
		}
	}

}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */