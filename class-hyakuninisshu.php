<?php
/**
 * Plugin Name: Hyakunin Isshu Admin Bar
 * Plugin URI:  https://wordpress.org/plugins/hyakunin-isshu-admin-bar/
 * Description: A plugin that randomly displays Hyakunin Isshu in the admin bar.
 * Version:     1.13
 * Author:      Katsushi Kawamori
 * Author URI:  https://riverforest-wp.info/
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hyakunin-isshu-admin-bar
 *
 * @package Hyakunin Isshu Admin Bar
 */

/*
	Copyright (c) 2023- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

$hyakuninisshu = new HyakuninIsshu();

/** ==================================================
 * Main
 */
class HyakuninIsshu {

	/** ==================================================
	 * Construct
	 *
	 * @since 1.00
	 */
	public function __construct() {

		add_action( 'admin_bar_menu', array( $this, 'hyakunin_isshu_admin_bar' ), 9999, 1 );
		add_action( 'rest_api_init', array( $this, 'register_rest' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 10, 1 );
	}

	/** ==================================================
	 * Admin Bar
	 *
	 * @param object $wp_admin_bar  wp_admin_bar.
	 * @since 1.00
	 */
	public function hyakunin_isshu_admin_bar( $wp_admin_bar ) {

		if ( current_user_can( apply_filters( 'hyakunin_isshu_bar_user_can', 'manage_options' ) ) ) {

			$wp_admin_bar->add_node(
				array(
					'id' => 'hyakunin-isshu-bar-tanka',
					'title' => '<span id="hyakuninisshu"></span>',
				)
			);

		}
	}

	/** ==================================================
	 * Register Rest API
	 *
	 * @since 1.10
	 */
	public function register_rest() {

		register_rest_route(
			'rf/hyakunin_isshu_api',
			'/token',
			array(
				'methods' => 'GET',
				'callback' => array( $this, 'hyakunin_isshu_get' ),
				'permission_callback' => array( $this, 'rest_permission' ),
			)
		);
	}

	/** ==================================================
	 * Rest Permission
	 *
	 * @since 1.10
	 */
	public function rest_permission() {

		return current_user_can( apply_filters( 'hyakunin_isshu_bar_user_can', 'manage_options' ) );
	}

	/** ==================================================
	 * Load script
	 *
	 * @param string $hook_suffix  hook_suffix.
	 * @since 1.10
	 */
	public function admin_scripts( $hook_suffix ) {

		$asset_file = include plugin_dir_path( __FILE__ ) . 'guten/dist/hyakuninisshu.asset.php';

		wp_enqueue_style(
			'hyakuninisshu-style',
			plugin_dir_url( __FILE__ ) . 'guten/dist/hyakuninisshu.css',
			array( 'wp-components' ),
			'1.0.0',
		);

		wp_enqueue_script(
			'hyakuninisshu',
			plugin_dir_url( __FILE__ ) . 'guten/dist/hyakuninisshu.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		$random_poem = $this->random_one();

		wp_localize_script(
			'hyakuninisshu',
			'hyakuninisshu_data',
			array(
				'interval_sec' => apply_filters( 'hyakunin_isshu_bar_interval_sec', 60 ),
				'tanka' => $random_poem['tanka'],
				'author' => $random_poem['author'],
				'source' => $random_poem['source'],
				'subject' => $random_poem['subject'],
				'author_label' => apply_filters( 'hyakunin_isshu_bar_author_title', '作者' ),
				'source_label' => apply_filters( 'hyakunin_isshu_bar_source_title', '出典' ),
				'subject_label' => apply_filters( 'hyakunin_isshu_bar_subject_title', '主題' ),
			)
		);
	}

	/** ==================================================
	 * Hyakunin Isshu API get
	 *
	 * @since 1.10
	 */
	public function hyakunin_isshu_get() {

		$random_poem = $this->random_one();

		return new WP_REST_Response( $random_poem, 200 );
	}

	/** ==================================================
	 * Random one of Hyakunin Isshu
	 *
	 * @since 1.10
	 */
	private function random_one() {

		$hyakunin_isshu = apply_filters( 'hyakunin_isshu_bar_array', $this->hyakunin_arr() );
		$random_index = array_rand( $hyakunin_isshu );
		$random_poem = $hyakunin_isshu[ $random_index ];

		return $random_poem;
	}

	/** ==================================================
	 * Array of Hyakunin Isshu
	 *
	 * @since 1.00
	 */
	private function hyakunin_arr() {

		$hyakunin_isshu = array(
			array(
				'tanka' => '秋の田の かりほの庵の とまをあらみ わがころもでは 露にぬれつゝ',
				'author' => '天智天皇',
				'source' => '後撰集',
				'subject' => '秋の夜の農夫のつらさ',
			),
			array(
				'tanka' => '春すぎて 夏来にけらし 白妙の 衣ほすてふ 天の香具山',
				'author' => '持統天皇',
				'source' => '出典不明',
				'subject' => 'すがすがしい夏の訪れ',
			),
			array(
				'tanka' => '足引きの 山鳥の尾の しだりおの ながながし夜を ひとりかもねん',
				'author' => '柿本人麿',
				'source' => '拾遺集',
				'subject' => 'ひとり寝のわびしさ',
			),
			array(
				'tanka' => '田子の浦に うち出てみれば 白妙の ふじのたかねに 雪はふりつゝ',
				'author' => '山部赤人',
				'source' => '新古今集',
				'subject' => '富士山の崇高な美しさ',
			),
			array(
				'tanka' => 'おくやまに 紅葉ふみわけ 鳴く鹿の 声きく時ぞ 秋は悲しき',
				'author' => '猿丸大夫',
				'source' => '古今集',
				'subject' => 'あでやかで寂しい秋',
			),
			array(
				'tanka' => 'かさゝぎの わたせる橋に 置く霜の 白きを見れば 夜ぞふけにける',
				'author' => '中納言家持',
				'source' => '出典不明',
				'subject' => '幻想的な冬の夜更け',
			),
			array(
				'tanka' => '天の原 ふりさけ見れば 春日なる 三笠の山に いでし月かも',
				'author' => '安倍仲麿',
				'source' => '古今集',
				'subject' => '月に託す望郷の思い',
			),
			array(
				'tanka' => 'わが庵は 都のたつみ しかぞすむ 世をうぢ山と 人はいふなり',
				'author' => '喜撰法師',
				'source' => '古今集',
				'subject' => '悠々自適の境地',
			),
			array(
				'tanka' => '花の色は うつりにけりな いたづらに わが身よにふる ながめせしまに',
				'author' => '小野小町',
				'source' => '出典不明',
				'subject' => '衰えゆく容色への嘆き',
			),
			array(
				'tanka' => 'これやこの 行くも帰るも 別れては しるもしらぬも 相坂の関',
				'author' => '蝉丸',
				'source' => '後撰集',
				'subject' => '逢坂の関にまつわる思い',
			),
			array(
				'tanka' => 'わたのはら 八十嶋かけて こぎ出ぬと 人には告げよ あまのつりぶね',
				'author' => '参議篁',
				'source' => '古今集',
				'subject' => '孤独・絶望の舟旅（隠岐への島流し）',
			),
			array(
				'tanka' => 'あまつ風 雲のかよひ路 吹きとぢよ 乙女のすがた しばしとゞめん',
				'author' => '僧正遍昭',
				'source' => '古今集',
				'subject' => '奇抜な発想と幻想美',
			),
			array(
				'tanka' => 'つくばねの 峰より落つる みなの川 こひぞつもりて 淵となりぬる',
				'author' => '陽成院',
				'source' => '後撰集',
				'subject' => '積もり積もった恋心',
			),
			array(
				'tanka' => 'みちのくの しのぶもぢずり 誰ゆへに みだれそめにし 我ならなくに',
				'author' => '河原左大臣',
				'source' => '古今集',
				'subject' => 'あなたゆえの恋心',
			),
			array(
				'tanka' => '君がため 春の野に出て 若菜つむ わが衣手に 雪はふりつゝ',
				'author' => '光孝天皇',
				'source' => '古今集',
				'subject' => '早春のさわやかさと心づかい',
			),
			array(
				'tanka' => '立ちわかれ いなばの山の 嶺におふる まつとし聞かば 今かへりこむ',
				'author' => '中納言行平',
				'source' => '古今集',
				'subject' => '旅立ちに際しての惜別の情',
			),
			array(
				'tanka' => 'ちはやぶる 神代もきかず 龍田川 からくれなゐに 水くゞるとは',
				'author' => '在原業平朝臣',
				'source' => '古今集',
				'subject' => '紅葉の不思議な華麗さ',
			),
			array(
				'tanka' => '住の江の 岸による波 よるさへや 夢の通ひ路 人目よくらむ',
				'author' => '藤原敏行朝臣',
				'source' => '古今集',
				'subject' => '忍ぶ恋のもどかしさ',
			),
			array(
				'tanka' => '難波潟 みじかきあしの ふしのまも あはでこの世を 過ぐしてよとや',
				'author' => '伊勢',
				'source' => '新古今集',
				'subject' => '激しく迫る恨みの歌',
			),
			array(
				'tanka' => 'わびぬれば 今はた同じ 難波なる 身をつくしても あはむとぞ思ふ',
				'author' => '元良親王',
				'source' => '後撰集',
				'subject' => '身を捨ててもという激情',
			),
			array(
				'tanka' => '今来むと 言ひしばかりに 長月の 有明の月を 待ちいでつるかな',
				'author' => '素性法師',
				'source' => '古今集',
				'subject' => '訪ねて来ない男への恨み言',
			),
			array(
				'tanka' => '吹くからに 秋の草木の しほるれば むべ山風を あらしと云らむ',
				'author' => '文屋康秀',
				'source' => '古今集',
				'subject' => '嵐という言葉の字解き',
			),
			array(
				'tanka' => '月みれば 千々に物こそ 悲しけれ わが身ひとつの 秋にはあらねど',
				'author' => '大江千里',
				'source' => '古今集',
				'subject' => '物思いに沈む孤独な秋の夜',
			),
			array(
				'tanka' => 'このたびは ぬさもとりあへず 手向山 紅葉のにしき かみのまにまに',
				'author' => '菅家',
				'source' => '古今集',
				'subject' => '錦のような紅葉の美しさ',
			),
			array(
				'tanka' => '名にしおはゞ 相坂山の さねかづら 人にしられで くるよしもがな',
				'author' => '三条右大臣',
				'source' => '後撰集',
				'subject' => '人目を忍んで逢いたい思い',
			),
			array(
				'tanka' => '小倉山 峰のもみぢば こころあらば 今ひとたびの みゆきまたなん',
				'author' => '貞信公',
				'source' => '拾遺集',
				'subject' => '紅葉の美しさと行幸の勧め',
			),
			array(
				'tanka' => 'みかのはら わきてながるゝ 泉河 いつ見きとてか こひしかるらむ',
				'author' => '中納言兼輔',
				'source' => '新古今集',
				'subject' => 'まだ見ぬ女性への恋心',
			),
			array(
				'tanka' => '山里は 冬ぞさびしさ まさりける 人めもくさも かれぬとおもへば',
				'author' => '源宗于朝臣',
				'source' => '古今集',
				'subject' => '冬の寂しさ、孤独感',
			),
			array(
				'tanka' => '心あてに をらばやおらむ 初霜の をきまどはせる しらぎくの花',
				'author' => '凡河内躬恒',
				'source' => '古今集',
				'subject' => '霜と白菊、白の美しさ',
			),
			array(
				'tanka' => '有明の つれなくみえし 別れより 暁ばかり うきものはなし',
				'author' => '壬生忠岑',
				'source' => '古今集',
				'subject' => '無常な女性への恨み言',
			),
			array(
				'tanka' => 'あさぼらけ 有明の月と みるまでに よしのの里に ふれるしら雪',
				'author' => '坂上是則',
				'source' => '古今集',
				'subject' => '吉野の里の雪景色の美しさ',
			),
			array(
				'tanka' => '山川に 風のかけたる しがらみは ながれもあへぬ 紅葉なりけり',
				'author' => '春道列樹',
				'source' => '古今集',
				'subject' => '散りたまる紅葉の美しさ',
			),
			array(
				'tanka' => 'ひさかたの ひかりのどけき 春の日に しづ心なく 花のちるらむ',
				'author' => '紀友則',
				'source' => '古今集',
				'subject' => '散り急ぐ桜の風情',
			),
			array(
				'tanka' => '誰をかも しる人にせむ 高砂の 松もむかしの ともならなくに',
				'author' => '藤原興風',
				'source' => '古今集',
				'subject' => '老残の孤独を嘆く',
			),
			array(
				'tanka' => '人はいさ こころもしらず 故郷は はなぞむかしの かに匂ひける',
				'author' => '紀貫之',
				'source' => '古今集',
				'subject' => '移ろいやすい人の心と不変の自然美',
			),
			array(
				'tanka' => '夏の夜は まだ宵ながら 明けぬるを 雲のいづくに 月やどるらむ',
				'author' => '清原深養父',
				'source' => '古今集',
				'subject' => '誇張された夏の短夜',
			),
			array(
				'tanka' => '白露に 風のふきしく 秋のゝは つらぬきとめぬ 玉ぞちりける',
				'author' => '文屋朝康',
				'source' => '後撰集',
				'subject' => '白露こぼれる秋の野の美',
			),
			array(
				'tanka' => '忘らるゝ 身をば思はず ちかひてし 人のいのちの おしくもあるかな',
				'author' => '右近',
				'source' => '拾遺集',
				'subject' => '心変わりした相手をなお案じる女心',
			),
			array(
				'tanka' => '浅茅生の をのゝしのはら 忍ぶれど あまりてなどか 人のこひしき',
				'author' => '参議等',
				'source' => '後撰集',
				'subject' => '忍んでも忍びきれない恋心',
			),
			array(
				'tanka' => 'しのぶれど 色に出にけり わが恋は 物や思ふと 人の問ふまで',
				'author' => '平兼盛',
				'source' => '拾遺集',
				'subject' => '包み隠せぬ恋心',
			),
			array(
				'tanka' => '恋すてふ 我名はまだき 立ちにけり 人しれずこそ 思ひ初めしか',
				'author' => '壬生忠見',
				'source' => '拾遺集',
				'subject' => '恋の噂にとまどう',
			),
			array(
				'tanka' => 'ちぎりきな かたみに袖を しぼりつゝ 末の松山 なみこさじとは',
				'author' => '清原元輔',
				'source' => '後拾遺集',
				'subject' => '心変わりを責める',
			),
			array(
				'tanka' => 'あひ見ての 後の心に くらぶれば むかしは物を 思はざりけり',
				'author' => '中納言敦忠',
				'source' => '拾遺集',
				'subject' => '結ばれたあとの物思いのつらさ',
			),
			array(
				'tanka' => 'あふことの たえてしなくは 中々に 人をも身をも うらみざらまし',
				'author' => '中納言朝忠',
				'source' => '拾遺集',
				'subject' => '深い仲ゆえの恨み',
			),
			array(
				'tanka' => '哀れとも いふべき人は おもほえで みのいたづらに なりぬべき哉',
				'author' => '謙徳公',
				'source' => '拾遺集',
				'subject' => 'つれない女性への恋の嘆き',
			),
			array(
				'tanka' => '由良のとを 渡る舟人 かぢをたえ 行へもしらぬ 恋のみちかな',
				'author' => '曾根好忠',
				'source' => '新古今集',
				'subject' => '頼りない、恋のなりゆき',
			),
			array(
				'tanka' => 'やへむぐら しげれる宿の さびしきに 人こそ見えね あきは来にけり',
				'author' => '恵慶法師',
				'source' => '拾遺集',
				'subject' => '荒れはてた住まいに、寂しい秋',
			),
			array(
				'tanka' => '風をいたみ 岩うつ波の をのれのみ くだけてものを おもふころかな',
				'author' => '源重之',
				'source' => '詞花集',
				'subject' => '冷淡な女にひとり思い悩む',
			),
			array(
				'tanka' => 'みかきもり 衛士のたく火の 夜はもえ 昼は消えつゝ 物をこそおもへ',
				'author' => '大中臣能宣朝臣',
				'source' => '詞花集',
				'subject' => '日ごと夜ごとの恋の悩み',
			),
			array(
				'tanka' => '君がため おしからざりし 命さへ ながくもがなと おもひぬる哉',
				'author' => '藤原義孝',
				'source' => '後拾遺集',
				'subject' => '恋を得た思いの変化',
			),
			array(
				'tanka' => 'かくとだに えやはいぶきの さしも草 さしもしらじな もゆる思ひを',
				'author' => '藤原実方朝臣',
				'source' => '後拾遺集',
				'subject' => '燃ゆる思いを告白',
			),
			array(
				'tanka' => '明けぬれば くるゝものとは しりながら なをうらめしき あさぼらけかな',
				'author' => '藤原道信朝臣',
				'source' => '後拾遺集',
				'subject' => '別れて帰る夜明けのつらさ',
			),
			array(
				'tanka' => 'なげきつゝ ひとりぬるよの 明くるまは いかに久しき ものとかはしる',
				'author' => '右大将道綱母',
				'source' => '拾遺集',
				'subject' => '独り寝のさびしさを訴える',
			),
			array(
				'tanka' => 'わすれじの 行末迄は かたければ けふをかぎりの 命ともがな',
				'author' => '儀同三司母',
				'source' => '新古今集',
				'subject' => '今の幸せを抱いて死にたい',
			),
			array(
				'tanka' => '滝の音は 絶えて久しく なりぬれど 名こそながれて なをきこえけれ',
				'author' => '大納言公任',
				'source' => '拾遺集',
				'subject' => '滝がよびおこす懐旧の情',
			),
			array(
				'tanka' => 'あらざらむ このよのほかの 思ひ出に 今ひとたびの あふこともがな',
				'author' => '和泉式部',
				'source' => '後拾遺集',
				'subject' => '病床での激しい愛',
			),
			array(
				'tanka' => 'めぐりあひて 見しやそれとも 分かぬまに 雲がくれにし 夜半の月かな',
				'author' => '紫式部',
				'source' => '新古今集',
				'subject' => '幼友達へのなつかしさ',
			),
			array(
				'tanka' => 'ありま山 いなの篠原 風吹けば いでそよ人を わすれやはする',
				'author' => '大弐三位',
				'source' => '後拾遺集',
				'subject' => '冷たい男への反発と愛',
			),
			array(
				'tanka' => 'やすらはで ねなましものを さよふけて かたぶくまでの 月を見しかな',
				'author' => '赤染衛門',
				'source' => '後拾遺集',
				'subject' => '月にこめた男への恨み',
			),
			array(
				'tanka' => '大江山 いくのゝ道の とをければ まだふみもみず 天のはしだて',
				'author' => '小式部内侍',
				'source' => '金葉集',
				'subject' => '才女の機知',
			),
			array(
				'tanka' => 'いにしへの ならの都の 八重桜 けふ九重に にほひぬるかな',
				'author' => '伊勢大輔',
				'source' => '詞花集',
				'subject' => '今日の八重桜の美を歌う',
			),
			array(
				'tanka' => 'よをこめて 鳥の空音は はかるとも よにあふさかの 関はゆるさじ',
				'author' => '清少納言',
				'source' => '後拾遺集',
				'subject' => '才知で男への切り返し',
			),
			array(
				'tanka' => '今はたゞ おもひ絶なん とばかりを 人づてならで いふよしもがな',
				'author' => '左京大夫道雅',
				'source' => '後拾遺集',
				'subject' => '禁じられた恋の苦悩',
			),
			array(
				'tanka' => '朝ぼらけ 宇治のかはぎり たえだえに あらはれわたる 瀬々の網代木',
				'author' => '権中納言定頼',
				'source' => '千載集',
				'subject' => '宇治川の夜明けの情景',
			),
			array(
				'tanka' => '恨みわび ほさぬ袖だに あるものを 恋にくちなん 名こそおしけれ',
				'author' => '相模',
				'source' => '後拾遺集',
				'subject' => '恋の浮名を惜しむ',
			),
			array(
				'tanka' => 'もろともに 哀れと思へ 山桜 花よりほかに 知る人もなし',
				'author' => '前大僧正行尊',
				'source' => '金葉集',
				'subject' => '人恋しく思う修験者',
			),
			array(
				'tanka' => '春の夜の 夢ばかりなる 手枕に かひなくたゝむ 名こそ惜しけれ',
				'author' => '周防内侍',
				'source' => '千載集',
				'subject' => '男の発想をそらせた即興の歌',
			),
			array(
				'tanka' => '心にも あらでこのよに ながらへば こひしかるべき よはの月かな',
				'author' => '三条院',
				'source' => '後拾遺集',
				'subject' => '月に託す絶望の嘆き',
			),
			array(
				'tanka' => 'あらし吹く 三室の山の もみぢばゝ 龍田の川の にしきなりけり',
				'author' => '能因法師',
				'source' => '後拾遺集',
				'subject' => '龍田川の紅葉の華麗さ',
			),
			array(
				'tanka' => 'さびしさに 宿を立出て 詠むれば いづくもおなじ あきのゆふぐれ',
				'author' => '良暹法師',
				'source' => '後拾遺集',
				'subject' => '秋の夕暮れの寂しさ',
			),
			array(
				'tanka' => '夕されば 門田の稲葉 をとづれて あしのまろやに 秋風ぞふく',
				'author' => '大納言経信',
				'source' => '金葉集',
				'subject' => '秋風のすがすがしさ',
			),
			array(
				'tanka' => '音にきく たかしの浜の あだ波は かけじや袖の ぬれもこそすれ',
				'author' => '祐子内親王家紀伊',
				'source' => '金葉集',
				'subject' => '言い寄る男を軽くいなす',
			),
			array(
				'tanka' => '高砂の 尾上の桜 さきにけり とやまの霞 たゝずもあらなん',
				'author' => '前中納言匡房',
				'source' => '後拾遺集',
				'subject' => '山桜の美しさを愛す',
			),
			array(
				'tanka' => 'うかりける 人をはつせの 山をろし風 はげしかれとは 祈らぬものを',
				'author' => '源俊頼朝臣',
				'source' => '千載集',
				'subject' => '祈っても実らぬ恋の嘆き',
			),
			array(
				'tanka' => 'ちぎりをきし させもが露を 命にて あはれことしの 秋もいぬめり',
				'author' => '藤原基俊',
				'source' => '千載集',
				'subject' => '約束が果たされる嘆き',
			),
			array(
				'tanka' => '和田の原 こぎ出てみれば ひさかたの くもゐにまがふ 奥津白波',
				'author' => '法性寺入道前関白太政大臣',
				'source' => '詞花集',
				'subject' => '空と海、壮大な大自然',
			),
			array(
				'tanka' => '瀬をはやみ 岩にせかるゝ 滝川の われてもすゑに あはむとぞおもふ',
				'author' => '崇徳院',
				'source' => '詞花集',
				'subject' => '将来を誓う恋の激情',
			),
			array(
				'tanka' => '淡路嶋 かよふ千鳥の なく声に 幾夜ね覚ぬ すまの関守',
				'author' => '源兼昌',
				'source' => '金葉集',
				'subject' => '寂しい冬の須磨の関',
			),
			array(
				'tanka' => '秋風に たなびく雲の たえまより もれいづる月の かげのさやけさ',
				'author' => '左京大夫顕輔',
				'source' => '新古今集',
				'subject' => '秋の夜の月の情趣',
			),
			array(
				'tanka' => '長からむ 心もしらず くろかみの みだれてけさは 物をこそ思へ',
				'author' => '待賢門院堀川',
				'source' => '千載集',
				'subject' => '恋するがゆえの不安',
			),
			array(
				'tanka' => 'ほととぎす なきつるかたを ながむれば たゞありあけの 月ぞのこれる',
				'author' => '後徳大寺左大臣',
				'source' => '千載集',
				'subject' => 'ほととぎすと有明の月',
			),
			array(
				'tanka' => '思ひわび さてもいのちは ある物を うきにたへぬは なみだなりけり',
				'author' => '道因法師',
				'source' => '千載集',
				'subject' => 'ままならぬ恋のつらさ',
			),
			array(
				'tanka' => '世の中よ 道こそなけれ おもひ入る やまのおくにも 鹿ぞなくなる',
				'author' => '皇太后宮大夫俊成',
				'source' => '千載集',
				'subject' => '行き場所もない無常の嘆き',
			),
			array(
				'tanka' => 'ながらへば またこのごろや しのばれん うしと見しよぞ いまは恋しき',
				'author' => '藤原清輔朝臣',
				'source' => '新古今集',
				'subject' => '悲しみをぬぐい去る時',
			),
			array(
				'tanka' => 'よもすがら 物思ふころは 明けやらぬ 閨のひまさへ つれなかりけり',
				'author' => '俊恵法師',
				'source' => '千載集',
				'subject' => '悶々としてすべてを恨む',
			),
			array(
				'tanka' => 'なげけとて 月やは物を 思はする かこちがほなる わがなみだかな',
				'author' => '西行法師',
				'source' => '千載集',
				'subject' => '月を見て恋人を恨む',
			),
			array(
				'tanka' => '村雨の 露もまだひぬ まきのはに 霧たちのぼる あきのゆふぐれ',
				'author' => '寂蓮法師',
				'source' => '新古今集',
				'subject' => '晩秋の深山の静寂',
			),
			array(
				'tanka' => '難波江の あしのかりねの ひとよゆへ 身をつくしてや 恋わたるべき',
				'author' => '皇嘉門院別当',
				'source' => '千載集',
				'subject' => '一夜の仮寝ゆえの身悶え',
			),
			array(
				'tanka' => '玉の緒よ 絶なば絶ね ながらへば 忍ぶることの よはりもぞする',
				'author' => '式子内親王',
				'source' => '新古今集',
				'subject' => '忍ぶ恋のつらさ',
			),
			array(
				'tanka' => '見せばやな をじまのあまの 袖だにも ぬれにぞぬれし 色はかはらず',
				'author' => '殷富門院大輔',
				'source' => '千載集',
				'subject' => '恋の苦しさを訴える',
			),
			array(
				'tanka' => 'きりぎりす なくや霜夜の さむしろに 衣かたしき ひとりかもねん',
				'author' => '後京極摂政前太政大臣',
				'source' => '新古今集',
				'subject' => '晩秋の霜夜の孤独',
			),
			array(
				'tanka' => '我袖は しほひに見えぬ おきの石の 人こそしらね かはくまもなし',
				'author' => '二条院讃岐',
				'source' => '千載集',
				'subject' => '片思いの悲しみ',
			),
			array(
				'tanka' => '世の中は つねにもがもな なぎさこぐ あまのをぶねの 綱手かなしも',
				'author' => '鎌倉右大臣',
				'source' => '新勅撰集',
				'subject' => '世の無常を思う',
			),
			array(
				'tanka' => 'みよしのゝ 山の秋風 さよふけて 故郷さむく ころもうつなり',
				'author' => '参議雅経',
				'source' => '新古今集',
				'subject' => '寂しさを強める秋の夜の音',
			),
			array(
				'tanka' => 'おほけなく 浮世の民に おほふかな わがたつそまに すみぞめの袖',
				'author' => '大僧正慈円',
				'source' => '千載集',
				'subject' => '僧としての決意',
			),
			array(
				'tanka' => '花さそふ あらしの庭の 雪ならで ふり行くものは 我身なりけり',
				'author' => '入道前太政大臣',
				'source' => '新勅撰集',
				'subject' => 'あわれな老残',
			),
			array(
				'tanka' => 'こぬ人を まつほの浦の 夕なぎに やくやもしほの 身もこがれつゝ',
				'author' => '権中納言定家',
				'source' => '新勅撰集',
				'subject' => '身も心も思いこがれる',
			),
			array(
				'tanka' => '風そよぐ ならの小川の 夕暮は みそぎぞ夏の しるしなりける',
				'author' => '従二位家隆',
				'source' => '新勅撰集',
				'subject' => '秋の気配が漂う夏の終り',
			),
			array(
				'tanka' => '人もおし 人も恨めし あぢきなく よをおもふゆへに 物思ふ身は',
				'author' => '後鳥羽院',
				'source' => '続後撰集',
				'subject' => '乱世の天皇の嘆き',
			),
			array(
				'tanka' => '百敷や ふるき軒端の しのぶにも なをあまりある 昔なりけり',
				'author' => '順徳院',
				'source' => '続後撰集',
				'subject' => '皇室の衰微を嘆く',
			),
		);

		return $hyakunin_isshu;
	}
}
