<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $file = \Storage::get('member.json');
        $lines = explode("\n", $file);
        foreach ($lines as $line) {
        	$obj = json_decode($line);
        	if (empty($obj->_id)) continue;
        	if (!is_string($obj->_id)) {
        		$tmp = (array) $obj->_id;
        		$obj->_id = $tmp['$oid'];
        	}
        	if (empty($obj->_id)) continue;

        	$data = ['api_token'    => $obj->_id,
        	         'device_token' => isset($obj->token) ? $obj->token : null,
        	         'imei'         => (isset($obj->imei) && strpos($obj->imei, '-') == false) ? $obj->imei : null,
        	         'firebase_uid' => $obj->_id,
        	         'nickname'     => isset($obj->nickname) ? $obj->nickname : '',
        	         'email'        => isset($obj->email) ? $obj->email : null,
        	         'phone'        => isset($obj->phone) ? str_replace(' ', '', $obj->phone) : null,
        	         'location_lat' => (isset($obj->location) && isset($obj->location->lat)) ? $obj->location->lat : null,
        	         'location_lng' => (isset($obj->location) && isset($obj->location->lng)) ? $obj->location->lng : null,
        	         'is_vendor'    => isset($obj->is_vendor) ? $obj->is_vendor : 0,
        	         'is_shrimper'  => isset($obj->is_shrimper) ? $obj->is_shrimper : 0,
                     'is_recycler'  => isset($obj->is_recycler) ? $obj->is_recycler : 0,
        	         'point'        => isset($obj->point) ? $obj->point : 0,
        	         'sale_count'   => $obj->sale ?? 0,
        	         'buy_count'    => $obj->buy ?? 0,
        	         'login_at'     => isset($obj->last_login_at) ? date('Y-m-d H:i:s', $obj->last_login_at) : null,
        	         'created_at'   => isset($obj->jointimestemp) ? date('Y-m-d H:i:s', $obj->jointimestemp) : null];
        	if (! (\App\Models\Profile\Entities\User::where('firebase_uid', $data['firebase_uid'])
        	                                        ->exists()) )
        	{
        	    if (\App\Models\Profile\Entities\User::where('imei', $data['imei'])
        	                                         ->exists()) unset($data['imei']);
        	    if (\App\Models\Profile\Entities\User::where('phone', $data['phone'])
        	                                         ->exists()) unset($data['phone']);
        	    if (\App\Models\Profile\Entities\User::where('email', $data['email'])
        	                                         ->exists()) unset($data['email']);
        	    \App\Models\Profile\Entities\User::insert($data);
        	}
        }

        $file = \Storage::get('ads.json');
        $lines = explode("\n", $file);
        foreach ($lines as $line) {
        	$obj = json_decode($line);
        	if (empty($obj->_id)) continue;
        	if (empty($obj->uid)) continue;
        	if (empty($obj->start_time)) continue;
        	if (empty($obj->end_time)) continue;
        	if (strpos($obj->url, '//') == false) continue;
        	if ($obj->end_time != '1569772800') continue;
        	$data = ['category'     => $obj->categories,
        	         'url'          => isset($obj->url) ? $obj->url : null,
        	         'height'       => isset($obj->height) ? $obj->height : 1,
        	         'weight'       => isset($obj->strength) ? $obj->strength : 0,
        	         'image'        => isset($obj->image) ? $obj->image : null,
        	         'image_type'   => isset($obj->image_type) ? $obj->image_type : null,
        	         'location_lat' => (isset($obj->location) && isset($obj->location->lat)) ? $obj->location->lat : null,
        	         'location_lng' => (isset($obj->location) && isset($obj->location->lng)) ? $obj->location->lng : null,
        	         'begin_at'     => date('Y-m-d H:i:s', $obj->start_time),
        	         'end_at'       => isset($obj->end_time) ? date('Y-m-d H:i:s', $obj->end_time) : null];
        	$user = \App\Models\Profile\Entities\User::where('firebase_uid', $obj->uid)
        	                                         ->first();
        	if (empty($user)) continue;
        	$data['user_id'] = $user->id;
        	if ($data['user_id'] != 973) continue;
        	$data['end_at'] = '2020-06-30 23:59:59';
        	\App\Models\Ad\Entities\Ad::insert($data);
        }

        $file = \Storage::get('shrimp_farm.json');
        $lines = explode("\n", $file);
        foreach ($lines as $line) {
        	$obj = json_decode($line);
        	if (empty($obj->_id)) continue;
        	$data = ['name'         => $obj->name,
        	         'address'      => isset($obj->address) ? $obj->address : '',
        	         'phone'        => isset($obj->phone) ? str_replace(' ', '', $obj->phone) : '',
        	         'content'      => isset($obj->consumer_content) ? $obj->consumer_content : null,
        	         'news'         => isset($obj->placard) ? $obj->placard : null,
        	         'is_close'     => 0,
        	         'location_lat' => (isset($obj->location) && isset($obj->location->lat)) ? $obj->location->lat : null,
        	         'location_lng' => (isset($obj->location) && isset($obj->location->lng)) ? $obj->location->lng : null];
        	if (!is_numeric($data['phone'])) continue;
        	$farm = \App\Models\ShrimpFarm\Entities\ShrimpFarm::insert($data);
        	$farm_id = \DB::getPdo()->lastInsertId();
        	if (isset($obj->like_me)) {
        		foreach ($obj->like_me as $item) {
        			$user = \App\Models\Profile\Entities\User::where('firebase_uid', $item)
        	                                                 ->first();
        	        if (isset($user))
        	            \App\Models\Profile\Entities\ProfileLikeFarm::insert(['user_id'        => $user->id,
        	                                                                  'shrimp_farm_id' => $farm_id]);
        		}
        	}
        	if (isset($obj->evaluation)) {
        		foreach ($obj->evaluation as $key=>$item) {
        			$user = \App\Models\Profile\Entities\User::where('firebase_uid', $key)
        	                                                 ->first();
        	        if (isset($user))
        	            \App\Models\System\Entities\Evaluation::insert(['user_id'     => $user->id,
        	                                                            'host_type'   => 'App\Models\ShrimpFarm\Entities\ShrimpFarm',
        	                                                            'host_id'     => $farm_id,
        	                                                            'score'       => $item->score,
        	                                                            'description' => $item->text,
        	                                                            'created_at'  => isset($item->timestamp) ? date('Y-m-d H:i:s', $item->timestamp) : null]);
        		}
        	}
        	if (isset($obj->weekday_text)) {
        	    $data['news'] = $obj->weekday_text ."/n". $data['news'];
        	}
        }

        $file = \Storage::get('log.json');
        $lines = explode("\n", $file);
        foreach ($lines as $line) {
        	$obj = json_decode($line);
        	if (empty($obj->_id)) continue;
        	if (empty($obj->data)) continue;
        	$user = \App\Models\Profile\Entities\User::where('firebase_uid', $obj->_id)
        	                                         ->first();
        	if (empty($user)) continue;
        	if ($user->firebase_uid == 'Z9ZbvnvN8EOngcf93ggSeuZiGwB3') continue;
        	if ($user->firebase_uid == 'IkufRL9bXzY7uSzcdq87MPHawku2') continue;
        	foreach ($obj->data as $item) {
        	    if (empty($item->point)) continue;
        	    \App\Models\System\Entities\PointRecord::insert(['category'   => 'System',
        	                                                     'user_id'    => $user->id,
        	                                                     'point'      => $item->point,
        	                                                     'formData'   => isset($item->action) ? $item->action : null,
        	                                                     'created_at' => $item->post_timestemp_s]);
        	}
        }
        exit;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function download()
    {
		//Detect special conditions devices
		$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
		$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
		$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
		$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
		$webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");

		//do something with this information
		if( $iPod || $iPhone || $iPad){
		    return redirect('https://apps.apple.com/tw/app/%E8%9D%A6%E9%9C%B8/id1358611219');
		}else if($Android){
			return redirect('https://play.google.com/store/apps/details?id=com.ttg.shrimp_king&hl=zh_TW');
		}else if($webOS){
		}
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function privacy()
    {
        echo "
        非常歡迎您光臨「蝦霸ＡＰＰ」（以下簡稱本產品），為了讓您能夠安心使用本產品的各項服務與資訊，特此向您說明本產品的隱私權保護政策，以保障您的權益，請您詳閱下列內容：
        <p>一、隱私權保護政策的適用範圍</p>
        <p>隱私權保護政策內容，包括本產品如何處理在您使用網站服務時收集到的個人識別資料。隱私權保護政策不適用於本產品以外的相關連結網站，也不適用於非本產品所委託或參與管理的人員。
        <p>二、個人資料的蒐集、處理及利用方式</p>
        <p>• 當您造訪本產品或使用本產品所提供之功能服務時，我們將視該服務功能性質，請您提供必要的個人資料，並在該特定目的範圍內處理及利用您的個人資料；非經您書面同意，本產品不會將個人資料用於其他用途。</p>
        <p>• 本產品在您使用服務信箱、問卷調查等互動性功能時，會保留您所提供的姓名、電子郵件地址、聯絡方式及使用時間等。</p>
        <p>• 於一般瀏覽時，伺服器會自行記錄相關行徑，包括您使用連線設備的IP位址、使用時間、使用的瀏覽器、瀏覽及點選資料記錄等，做為我們增進網站服務的參考依據，此記錄為內部應用，決不對外公佈。</p>
        <p>• 為提供精確的服務，我們會將收集的問卷調查內容進行統計與分析，分析結果之統計數據或說明文字呈現，除供內部研究外，我們會視需要公佈統計數據及說明文字，但不涉及特定個人之資料。</p>
        <p>三、資料之保護</p>
        <p>• 本產品主機均設有防火牆、防毒系統等相關的各項資訊安全設備及必要的安全防護措施，加以保護網站及您的個人資料採用嚴格的保護措施，只由經過授權的人員才能接觸您的個人資料，相關處理人員皆簽有保密合約，如有違反保密義務者，將會受到相關的法律處分。</p>
        <p>• 如因業務需要有必要委託其他單位提供服務時，本產品亦會嚴格要求其遵守保密義務，並且採取必要檢查程序以確定其將確實遵守。</p>
        <p>四、網站對外的相關連結</p>
        <p>本產品的網頁提供其他網站的網路連結，您也可經由本產品所提供的連結，點選進入其他網站。但該連結網站不適用本產品的隱私權保護政策，您必須參考該連結網站中的隱私權保護政策。</p>
        <p>五、與第三人共用個人資料之政策</p>
        <p>本產品絕不會提供、交換、出租或出售任何您的個人資料給其他個人、團體、私人企業或公務機關，但有法律依據或合約義務者，不在此限。</p>
        <p>前項但書之情形包括不限於：</p>
        <p>• 經由您書面同意。</p>
        <p>• 法律明文規定。</p>
        <p>• 為免除您生命、身體、自由或財產上之危險。</p>
        <p>• 與公務機關或學術研究機構合作，基於公共利益為統計或學術研究而有必要，且資料經過提供者處理或蒐集著依其揭露方式無從識別特定之當事人。</p>
        <p>• 當您在網站的行為，違反服務條款或可能損害或妨礙網站與其他使用者權益或導致任何人遭受損害時，經網站管理單位研析揭露您的個人資料是為了辨識、聯絡或採取法律行動所必要者。</p>
        <p>• 有利於您的權益。</p>
        <p>• 本產品委託廠商協助蒐集、處理或利用您的個人資料時，將對委外廠商或個人善盡監督管理之責。</p>
        <p>六、Cookie之使用</p>
        <p>為了提供您最佳的服務，本產品會在您的電腦中放置並取用我們的Cookie，若您不願接受Cookie的寫入，您可在您使用的瀏覽器功能項中設定隱私權等級為高，即可拒絕Cookie的寫入，但可能會導至網站某些功能無法正常執行 。</p>
        <p>七、隱私權保護政策之修正</p>
        <p>本產品隱私權保護政策將因應需求隨時進行修正，修正後的條款將刊登於網站上。</p>";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
