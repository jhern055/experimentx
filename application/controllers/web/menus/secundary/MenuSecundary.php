<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MenuSecundary extends CI_Controller {

	public  $config;
	public  $page;
	public 	$registred_by;
	public 	$idTemp;
	public 	$sys;

    // menuSecundary
	public 	$uri_menuSecundary;
    // ..

	public function __construct() {
	parent::__construct();		

	$this->load->model("web/menus/secundary/menuSecundary_model");
	$this->load->library("pagination");
	$this->load->model("vars_system_model");
	$segment=(int)$this->uri->segment(2);
	$this->sys=$this->vars_system_model->_vars_system();
	
	// esto es porque tuve conflicto con el segmento 3 cuando era  segmento/segmento/segmento/segmento/
	$x =3; 
	while (is_string($segment) or empty($segment) ){
		$segment=$this->uri->segment($x)?(int)$this->uri->segment($x):""; $x++;

		if($x>=20){
		$segment=0;	
		break;
		}
	}


	$this->page=( (!empty($segment))? $segment:0);

	$this->now = date("Y-m-d H:i:s");
	$this->registred_by=$this->security->xss_clean($this->session->userdata("user_id"));
	$this->idTemp=$this->session->userdata("idTemp");

    $uri_string=array_merge($_GET,$_POST);
    $uri_string=(!empty($uri_string["uri_string"])? $uri_string["uri_string"]:$this->uri->uri_string );

	// Sucursal <menuSecundary>
	$this->uri_menuSecundary="web/menus/secundary/";
	// ... </menuSecundary>

	}

	public function config_pagination($method,$amount,$per_page) {

	$config["base_url"] = base_url() .$method;
	$config['total_rows'] = $amount;
	$config["per_page"] = $per_page;
	// pr(str_replace("/", "_", $method));
	/* This Application Must Be Used With BootStrap 3 * esto es bootstrap 3 */
	$config['full_tag_open'] = "<ul class='pagination pagination-small pagination-centered ".str_replace("/", "_", $method)."' data-paginations_div='1'>";
	$config['full_tag_close'] ="</ul>";
	$config['num_tag_open'] = '<li>';
	$config['num_tag_close'] = '</li>';
	$config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#' class='not-active'>";
	$config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
	$config['next_tag_open'] = "<li>";
	$config['next_tag_close'] = "</li>";
	$config['prev_tag_open'] = "<li>";
	$config['prev_tag_close'] = " </li>";
	$config['first_tag_open'] = "<li>";
	$config['first_tag_close'] = "</li>";
	$config['last_tag_open'] = "<li>";
	$config['last_tag_close'] = "</li>";

	return $config;

	}
	// insertar o actualizar
	public function do_it($id=null,$method=null) {
	$http_params=$_POST;

	$http_params   =array(
	"MODE"         =>"do_it",
	"id"           =>(!empty($http_params["id"]) ?decode_id( strip_tags( $this->security->xss_clean($http_params["id"]) ) ) :""),
	"name"         =>(!empty($http_params["name"]) ?strip_tags( $this->security->xss_clean($http_params["name"]) ):""),
	);

	extract($http_params);

		if(!empty($id)):
		$data_depend=array("updated_by" =>$this->registred_by,"updated_on" =>$this->now);
		else:
		$data_depend=array("registred_by" =>$this->registred_by,"registred_on" =>$this->now);
		endif;

	// menuSecundaryView
		if($method=="menuSecundaryView"){
		
		// Aqui almacenamos en el arreglo el cual vamos a insertar o actualizar
			$data=array(
			"name"=>$name,
			);

		// validar que  no exista un registro identico 
		if($this->menuSecundary_model->record_same_menuSecundary($data,$id) )
		return array("status"=>0,"msg"=>"Ya existe un registro identico ","data"=>false);

		$data=array_merge($data_depend,$data);

				if($id)
				$last_id=$this->menuSecundary_model->update_menuSecundary($data,$id);
				else
				$last_id=$this->menuSecundary_model->insert_menuSecundary($data);

		return array("status"=>1,"msg"=>"Exito","data"=>$last_id);

		}
	// </menuSecundaryView> 		
	}	

	public function children($module) {

	$module=(!empty($module)?$module:"web/menus/secundary/");

	$data["module"]=$module;	

	$data["module_data"]=$this->load->module_text_from_id($module);
	$data["module_childrens"]=$this->load->get_module_childrens($data["module_data"]["id"]);

	if(empty($data["module_childrens"]))
	redirect($module);

	if(!empty($_POST["ajax"]))
	return print_r(json_encode(array("status"=>1,"msg"=>"HtmlConExito","html"=>$this->load->view('recycled/menu/Module_children',$data,true) ))) ;
	else
	return $this->load->template('recycled/menu/Module_children',$data);

	}	

	// <menuSecundary>
	// carga ajax
	public function menuSecundary_ajax() {

	$module=$this->uri_menuSecundary;

	$http_params=array_merge($_GET,$_POST);
	$http_params          =array(
	"input_search_menuSecundary" =>(!empty($http_params["input_search_menuSecundary"]) ? strip_tags( $this->security->xss_clean( $http_params["input_search_menuSecundary"] ) ) :""),
	"show_amount_menuSecundary"  =>(!empty($http_params["show_amount_menuSecundary"]) ? strip_tags( $this->security->xss_clean( $http_params["show_amount_menuSecundary"])  ) :10),
	);
	extract($http_params);

	$page_amount=$show_amount_menuSecundary;

	$query_search=array(
		"\$this->db->like('name', \"".$input_search_menuSecundary."\");",
		);

	$this->pagination->initialize($this->config_pagination("web/menus/secundary/menuSecundary_ajax",$this->menuSecundary_model->get_menuSecundary_amount($query_search),$page_amount) );

	$data=array(
		"module"=>$module,
		"sys"=>$this->sys,
		"input_search_menuSecundary"=>$input_search_menuSecundary,
		"show_amount"=>$show_amount_menuSecundary,
		"records_array"=>$this->menuSecundary_model->get_menuSecundary($page_amount, $this->page,$query_search),
		"pagination"=>$this->pagination->create_links(),
		"module_data"=>$this->menuSecundary_model->m_name($module),
		);
	$data["module_data"]["module_data_method_do_it"]="web/menus/secundary/menuSecundaryView/";

    $data["modules_quick"]=$this->load->get_back_access($module);

	$html=$this->load->view("web/menus/secundary/ajax/table-menuSecundary-view",$data,true);
	$data["html"]=$html;

	// $this->session->set_userdata('record_start_row_menuSecundary',$this->page);

	if(!empty($_GET["ajax"]))
	echo $data["html"];
	else
	return $data;

	}

	// carga normal
	public function index() {
		
	$data=$this->menuSecundary_ajax();
	// pr($data);
	$this->session->set_userdata("idTemp");
	$this->session->set_userdata('input_search_menuSecundary');
	$this->session->set_userdata("sessionMode_menuSecundary");

	// if(!empty($this->page) and !empty($data["records_array"]))
	// $this->session->set_userdata('record_start_row_menuSecundary',$this->page);

	if(!empty($_POST["ajax"]))
	return print_r(json_encode(array("status"=>1,"msg"=>"HtmlConExito","html"=>$this->load->view('web/menus/secundary/menuSecundary_view',$data,true) ))) ;
	else
	return $this->load->template('web/menus/secundary/menuSecundary_view',$data);

	}
	// ver para registro
	public function menuSecundaryView($id=null) {

	$id_affected='';
	$module=$this->uri_menuSecundary;
	$array["module"]=$module;

	if(!empty($_POST["id"]))
    $id=decode_id( strip_tags( $this->security->xss_clean($_POST["id"]) ) );

    $MODE=(!empty($_POST["MODE"])?strip_tags( $this->security->xss_clean($_POST["MODE"]) ) : "");

	if($this->idTemp and $MODE!="add")
	$id=$this->idTemp;

    // <RightCheck> 
    if(!empty($MODE)):

        $module_do_it=(!empty($id)?"update":"insert");
        $return_valid=rights_validation($array["module"].$module_do_it,"ajax");

        if(!$return_valid["status"])
        return print_r(json_encode($return_valid));       

    endif;
    // </RightCheck> 

	if(!empty($MODE) and $MODE=="do_it"):

	// retorna el id que se vio afectado asi sea UPDATE o INSERT o si existe un registro identico
	$return=$this->do_it($id,"menuSecundaryView");

		if(!$return["status"])
		return print_r(json_encode($return));		
		else
		$id=$return["data"];

		$this->session->set_userdata("idTemp",$id);

	endif;

	// traer el registro 
	$array['data']=$this->menuSecundary_model->get_menuSecundary_id($id);

	if(!empty($MODE) and $MODE=="view")
	$array['data']['MODE']="do_it";
	else if (!empty($id))
	$array['data']['MODE']="view";
	else
	$array['data']['MODE']="do_it";

	// <F5>
	$sessionMode=$this->session->userdata("sessionMode_menuSecundary");
	if(!empty($_POST)):
		$this->session->set_userdata("sessionMode_menuSecundary",$array['data']['MODE']);
	endif;
	$array['id']=$id;
	// </F5>
	// nombre del modulo
	$array['data']["module_data"]=$this->menuSecundary_model->m_name($module);	
	$array['data']["module_data_method_do_it"]="web/menus/secundary/menuSecundaryView/";

		if(!empty($MODE)){

    	$this->load->model("vars_system_model");
		$array["data"]["sys"]=$this->vars_system_model->_vars_system();

	    $array['data']["modules_quick"]=$this->load->get_back_access($module);

		$html=$this->load->view($module."dinamyc-inputs",$array["data"],true);
		return print_r(json_encode( array("status"=>1,"html"=>$html,"id"=>$id) ));	

		}

	$this->load->template($module.'dinamyc-view',$array);

	}
	public function menuSecundary_delete() {

	$module=$this->uri_menuSecundary;

    // <RightCheck> 
        $return_valid=rights_validation($module."delete","ajax");

        if(!$return_valid["status"])
        return print_r(json_encode($return_valid));       
    // </RightCheck>

	if(!empty($_POST["id"]))
    $id=decode_id( strip_tags( $this->security->xss_clean($_POST["id"]) ) );

	if($this->menuSecundary_model->menuSecundary_delete_it($id))
	return print_r(json_encode( array("status"=>1,"msg"=>"Se elimino","data"=>false ) ));

	}		
// </menuSecundary>
}
