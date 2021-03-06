<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporting extends Admin_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->pages = 'reporting';
    }

	/*Controller for fetching data to the table*/

	public function index()
	{
		$summaries     = $this->reporting->fetch_data();

		$pages    = $this->pages;
		$main_view  = 'report/summary';

		$this->load->view('template', compact('main_view', 'pages', 'summaries'));
	}

	public function index_draft()
	{
		$drafts     = $this->reporting->fetch_data_draft();

		$pages    = $this->pages;
		$main_view  = 'report/report_draft';

		$this->load->view('template', compact('main_view', 'pages', 'drafts'));
	}

	public function index_books()
	{
		$books     = $this->reporting->fetch_data_book();

		$pages    = $this->pages;
		$main_view  = 'report/report_book';

		$this->load->view('template', compact('main_view', 'pages','books'));
	}

	public function index_author()
	{
		$author     = $this->reporting->fetch_data_author();

		$pages    = $this->pages;
		$main_view  = 'report/report_author';

		$this->load->view('template', compact('main_view', 'pages','author'));
	}

	public function index_hibah()
	{
		$hibah     = $this->reporting->fetch_data_hibah();

		$pages    = $this->pages;
		$main_view  = 'report/report_hibah';

		$this->load->view('template', compact('main_view', 'pages','hibah'));
	}

	public function performa_editor()
	{

		$performance_editor = $this->reporting->select(['draft_title','username','edit_start_date','edit_deadline','edit_end_date'])->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'editor')->getAll('draft');

		$pages    = $this->pages;
		$main_view = 'report/performance_editor';

		$this->load->view('template', compact('main_view', 'pages', 'performance_editor'));

	}

	public function performa_layouter()
	{
		$performance_layouter = $this->reporting->select(['draft_title','username','layout_start_date','layout_deadline','layout_end_date'])->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'layouter')->getAll('draft');

		$pages    = $this->pages;
		$main_view = 'report/performance_layouter';

		$this->load->view('template', compact('main_view', 'pages', 'performance_layouter'));
	}

	public function getSummary()
	{
		$count_review = 0;
		$count_disetujui = 0;
		$count_editor = 0;
		$count_layout = 0;
		$count_proofread = 0;
		$count_book = 0;

		$result_review = $this->reporting->select(['draft_status'])->getAll('draft');
		foreach ($result_review as $hasil_review){
			if ($hasil_review->draft_status == 4 or $hasil_review->draft_status == 5) {
					$count_review++;
			}
		}
		$result['count_review'] = $count_review;

		$result_disetujui = $this->reporting->select(['is_review'])->getAll('draft');
		foreach ($result_disetujui as $hasil_disetujui) {
			if ($hasil_disetujui->is_review == 'y') {
				$count_disetujui++;
			}
		}
		$result['count_disetujui'] = $count_disetujui;

		$result_editor = $this->reporting->select(['is_review','is_edit','is_layout','is_proofread'])->getAll('draft');
		foreach ($result_editor as $hasil_editor) {
			if ($hasil_editor->is_review == 'y' AND $hasil_editor->is_edit == 'n') {
				$count_editor++;
			}
			if($hasil_editor->is_edit == 'y' AND $hasil_editor->is_layout == 'n'){
				$count_layout++;
			}
			if($hasil_editor->is_layout == 'y' AND $hasil_editor->is_proofread == 'n'){
				$count_proofread++;
			}
			if($hasil_editor->is_proofread == 'y'){
				$count_book++;
			}
		}
		$result['count_editor'] = $count_editor;
		$result['count_layout'] = $count_layout;
		$result['count_proofread'] = $count_proofread;
		$result['count_book'] = $count_book;

		echo json_encode($result);
	}

	public function getPie()
	{
		$count_ugm = 0;
		$count_lain = 0;

		$result_ugm = $this->reporting->select(['institute_id'])->getAll('author');
		foreach ($result_ugm as $institute_ugm){
			if ($institute_ugm->institute_id == 4) {
					$count_ugm++;
			}
			else {
				$count_lain++;
			}
		}
		$result['count_ugm'] = $count_ugm;
		$result['count_lain'] = $count_lain;

		echo json_encode($result);
	}

	public function getPieAuthorGelar(){
		$count_prof = 0;
		$count_doctor = 0;
		$count_lainnya = 0;

		$result_gelar = $this->reporting->select(['author_latest_education'])->getAll('author');
		foreach ($result_gelar as $gelar_ugm){
			if ($gelar_ugm->author_latest_education == 's4'){
				$count_prof++;
			}
			elseif ($gelar_ugm->author_latest_education == 's3'){
				$count_doctor++;
			}
			else {
				$count_lainnya++;
			}
		}
		$result['count_prof'] = $count_prof;
		$result['count_doctor'] = $count_doctor;
		$result['count_lainnya'] = $count_lainnya;

		echo json_encode($result);
	}

	public function getPieHibah()
	{
		$count_hibah = 0;
		$count_reguler = 0;

		$result_ugm = $this->reporting->select(['category_type'])->join3('category', 'draft', 'category')->getAll('draft');
		foreach ($result_ugm as $category_ugm){
			if ($category_ugm->category_type == 1) {
					$count_hibah++;
			}
			else {
					$count_reguler++;
			}
		}
		$result['count_hibah'] = $count_hibah;
		$result['count_reguler'] = $count_reguler;

		echo json_encode($result);
	}

	public function getDraft()
  {
		for($i = 1; $i <= 12; $i++)
		{
			$result[$i] = $this->reporting->getDraft($i);
			$result['count'][$i] = count($result[$i]);
		}
		echo json_encode($result);
  }

	public function getBook()
  {
		for($i = 1; $i <= 12; $i++)
		{
			$result[$i] = $this->reporting->getBook($i);
			$result['count'][$i] = count($result[$i]);
		}
		echo json_encode($result);
  }

	public function getAuthor()
  {
		for($i = 1; $i <= 3; $i++)
		{
			$result[$i] = $this->reporting->getAuthor($i);
			$result['count'][$i] = count($result[$i]);
		}
		echo json_encode($result);
  }

	public function getHibah()
	{
		for($i = 1; $i <= 3; $i++)
		{
			$result[$i] = $this->reporting->getHibah($i);
			$result['count'][$i] = count($result[$i]);
		}
		echo json_encode($result);
	}
}

// function filter($page = null){
// 	$filter   = $this->input->get('filter', true);
// 	$this->db->group_by('draft.draft_id');
// 	if($this->level == 'reviewer'){
// 					/*=============================================
// 					=            Filter level reviewer            =
// 					=============================================*/
// 					if($filter == 'sudah'){
// 							$drafts =array();
// 							$drafts_source = $this->draft->join('category')
// 							->join('theme')
// 							->join3('draft_reviewer','draft','draft')
// 							->join3('reviewer','draft_reviewer','reviewer')
// 							->join3('user','reviewer','user')
// 							->where('user.username',$this->username)
// 							->orderBy('draft_title')
// 							->paginate($page)
// 							->getAll();
// 					//cari tau rev 1 atau rev 2 yg sedang login
// 							foreach ($drafts_source as $key => $value) {
// 									$rev = $this->draft->getIdAndName('reviewer', 'draft_reviewer', $value->draft_id);
// 									$value->rev = key(array_filter(
// 											$rev,
// 											function ($e) {
// 													return $e->reviewer_id == $this->session->userdata('role_id');
// 											}
// 									));
// 									if($value->rev == 0){
// 										$value->review_flag = $value->review1_flag;
// 								}elseif($value->rev == 1){
// 										$value->review_flag = $value->review2_flag;
// 								}else{}
//
// 								if($value->review_flag != ''){
// 										$drafts[] =$value;
// 								}
// 								$total = count($drafts);
// 						}
// 				}elseif($filter == 'belum'){
// 					$drafts =array();
// 					$drafts_source = $this->draft->join('category')
// 					->join('theme')
// 					->join3('draft_reviewer','draft','draft')
// 					->join3('reviewer','draft_reviewer','reviewer')
// 					->join3('user','reviewer','user')
// 					->where('user.username',$this->username)
// 					->orderBy('draft_title')
// 					->paginate($page)
// 					->getAll();
// 					//cari tau rev 1 atau rev 2 yg sedang login
// 					foreach ($drafts_source as $key => $value) {
// 							$rev = $this->draft->getIdAndName('reviewer', 'draft_reviewer', $value->draft_id);
// 							$value->rev = key(array_filter(
// 									$rev,
// 									function ($e) {
// 											return $e->reviewer_id == $this->session->userdata('role_id');
// 									}
// 							));
// 							if($value->rev == 0){
// 								$value->review_flag = $value->review1_flag;
// 						}elseif($value->rev == 1){
// 								$value->review_flag = $value->review2_flag;
// 						}else{}
//
// 						if($value->review_flag == ''){
// 								$drafts[] =$value;
// 						}
// 						$total = count($drafts);
// 				}
// 		}
// 	}
// }

/* End of file Reporting.php */
/* Location: ./application/controllers/Reporting.php */
