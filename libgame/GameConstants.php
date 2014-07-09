<?php
// 1000以下的为保留错误，framework使用的
class GameStatusCode {
	//  未知错误
	const UNKNOWN_ERROR = 1;
	//已经使用过
	const HAS_USED = 2;
	// status constants definition
	const DATABASE_ERROR = 1000;
	// 用户不存在
	const USER_NOT_EXISTS = 1001;
	// 指定的item不存在
	const ITEM_NOT_EXISTS = 1002;
	// 动作不存在
	const ACTION_NOT_EXISTS = 1003;
	// 该条数据不存在
	const DATA_NOT_EXISTS = 1004;
	// 用户的item不足
	const ITEM_NOT_ENOUGH = 1005;
	// 用户的authcode已过期
	const AUTH_CODE_EXPIRED = 1006;
	// 用户的authcode错误
	const AUTH_CODE_ERROR = 1007;
	// memcache set failure
	const SET_MEMCACHE_ERROR = 1008;
	// 要送的礼物不存在
	const GIFT_NOT_EXISTS = 1009;
	// 客户端传的参数不正确
	const PARAMETER_ERROR = 1010;
	// 数据不正确
	const DATA_ERROR = 1011;
	// 购买方式不正确
	const BUY_METHOD_ERROR = 1012;
	// 需要刷新页面
	const NEED_REFRESH_PAGE = 1013;
	// 要送的礼物已经送过了
	const GIFT_HAS_SENT = 1014;
	// 动作被锁定，不能再次操作
	const ACTION_LOCKED = 1015;
	// 不是紫豆用户
	const USER_NOT_MULBERRY = 1016;
	// 指定的package不存在
	const PACKAGE_NOT_EXISTS = 1017;
	// 不是会员用户
	const USER_NOT_MEMBER = 1018;
	// 用户的game key错误
	const GAME_KEY_ERROR = 1019;
	// 没有购买加工厂
	const FACTORY_NOT_EXIST = 1020;
	
	// 任务不存在
	const TASK_NOT_EXIST = 1021;
	// 没有接受该任务
	const TASK_NOT_ACCEPT = 1022;
	// 任务 过期
	const TASK_HAS_EXPIRED = 1023;
	// 已经完成该任务
	const TASK_HAS_COMPLETED = 1024;
	// 超过了任务要求的级别
	const TASK_LEVEL_EXCEED = 1025;
	// 更新任务信息的时候出错
	const TASK_UPDATE_ERROR = 1026;
	
	// 使用的item不正确
	const ITEM_ERROR = 1027;
	// 不是物品的拥有者
	const NOT_OWNER = 1028;
	// 购买物品的数量错误
	const BUY_COUNT_ERROR = 1029;
	// 物品的等级不够
	const ITEM_LEVEL_ERROR = 1030;
	//物品不能够使用
	const CANNT_USE = 1031;
	
	// 已经有田地了
	const HAS_FIELD_AT_THIS_SPACE = 1053;
	// 用户已经偷过了
	const HAS_STOLEN = 1054;
	//作物到达最小产出，不能再偷了
	const MIN_OUTPUT = 1055;
	// 作物不需要浇水了
	const NO_NEED_TO_WATER = 1056;
	// 作物不需要除草
	const NO_NEED_TO_KILL_WEED = 1057;
	// 作物不需要杀虫
	const NO_NEED_TO_KILL_BAD = 1058;
	// 作物不需要施肥
	const NO_NEED_TO_FARTILIZE = 1059;
	// 不需要喂东西了
	const NO_NEED_TO_FEED = 1060;
	// 这块地不需要扩了
	const NO_NEED_TO_EXTEND = 1061;
	// 这块地不需要铲了
	const NO_NEED_TO_HOE = 1062;
	// 不能毁灭证据
	const CANT_DESTORY_EVIDENCE = 1100;
	// 不能再种草
	const CANT_PUT_WEED = 1101;
	// 不能再放虫
	const CANT_PUT_PEST = 1102;
	// 已经收获过了
	const HAS_HARVESTED = 1103;
	// 作物未成熟
	const IMMATUAL_CROP = 1104;
	// 不能再播种了
	const CANT_SEED = 1105;
	// 不能再买该物品
	const HAS_BOUGHT = 1106;
	// 不能再放置物品
	const CANT_PUT_ITEM = 1107;
	// 已经生产过了
	const HAS_PRODUCED = 1108;
	// 需要喂东西
	const NEED_TO_FEED = 1109;
	// 不能收获
	const CANT_HARVEST = 1110;
	// 不正确的item类型
	const INCORRECT_ITEM_TYPE = 1111;
	// 已经施过肥了
	const HAS_FERTILIZED = 1112;
	// 因为已经购买礼包中的一个装饰，不能再买该礼包
	const HAS_BOUGHT_ONE = 1113;
	// 因为已经领取每日礼包
	const HAS_RECEIVE = 1114;
	// 没有土地
	const NOT_OWN_FIELD = 1115;
	//装饰的合成功能不可用
	const DECO_FORMULA_NOT_AVAIL=1116;
	//装饰的增加体力功能不可用
	const DECO_STRENTH_NOT_AVAIL=1117;
	
	// 金币不够
	const COIN_NOT_ENOUGH = 1121;
	// money不够
	const MONEY_NOT_ENOUGH = 1122;
	//点卡不够
	const COUPON_NOT_ENOUGH = 1125;
	// 经验值不够
	const EXP_NOT_ENOUGH = 1123;
	// 魅力值不够
	const CHARM_NOT_ENOUGH = 1124;
	
	// 做坏事达到最大限制
	const ACTION_MAX_ALLOWED = 1131;
	// 偷东西被抓住
	const STEAL_CAUGHT = 1201;
	// 从不是好友关系
	const NOT_FRIEND = 1202;
	// 偷东西被狗狗盯上，不能再偷
	const STEAL_WATCH_BY_DOG = 1203;
	// 方式不对
	const STATUS_METHOD_ERROR=1204;
	// 不能够再扩地
	const CANT_EXTEND=1205;
	//动物没有死亡
	const ANIMAL_NOT_DIE=1206;
	//动物没有产出
	const ANIMAL_NO_OUTPUT=1206;
	//动物没有病
	const ANIMAL_NOT_ILL=1207;
	//动物的亲密度是满的
	const ANIMAL_INTIMATE_IS_FULL=1208;
	//动物有病
	const ANIMAL_HAS_ILL=1209;
	//没有东西可以偷
	const NOTHING_TO_STOLEN=1210;
	//帮助好友超过最大限度
	const OVER_MAX_ALLOWED=1211;
	//描述信息不存在
	const DEF_NOT_EXIST=1212;
	//清除的对象错误
	const ITEM_KILLED_ERROR=1213;
	//没有相应的工作
	const JOB_NOT_DEFINED = 1214;
	//已经雇佣了好友
	const FRIEND_ALREADY_EMPLOYED = 1215;
	//同一个工种不能定义两个好友来从事
	const JOB_ALREADY_EMPLOYED = 1216;
	//对应的升级物品暂时不支持
	const SWITCH_ITEM_TYPE_NOT_SUPPORT = 1217;
	//没有足够的体力值
	const STRENGTH_NOT_ENOUGH = 1218; 
	//前提任务没有完成
	const PREV_TASK_NOT_COMPLETED = 1219;
	//动作数量不够
	const ACTION_COUNT_NOT_ENOUGH = 1220;
	//用户等级不够
	const USER_LEVEL_NOT_ENOUGH = 1221;
	//这个植物不能够施肥
	const CROP_CAN_NOT_FERTILIZED = 1222;
	//超出了土地的最大拥有量
	const OVER_MAX_FILED_NUM = 1223;
	//超出了动物的最大拥有量
	const OVER_MAX_ANIMAL_NUM = 1224;
	//没有东西收获
	const NOTHING_TO_HARVEST = 1225;
	//好友工作等级不够，不能够雇佣
	const JOB_LEVEL_NOT_ENOUGH = 1226;
	//两个东西不匹配																				
	const NOT_MATCHING = 1227;
	//methd错误
	const METHOD_ERROR = 1228;
	//还未加工完成
	const NOT_PROCESS_ALREADY = 1229;
	//id错误
	const ID_ERROR = 1230;
	//物品不能够购买
	const CANT_BUY = 1231;
	
	//feed错误的相关的id
	//不是游戏玩家
	const NOT_APP_USER=1232;
	//feed超出范围
	const FEED_NOT_EXIST=1233;
	//好友没有发feed
	const FRIEND_NOT_SEND_FEED=1234;
	//feed的可响应次数已经满了
	const FEED_COUNT_IS_FULL=1235;
	//feed已经响应过了
	const FEED_HAS_USED=1236;
	//不是好友
	const NOT_USER_FRIEND=1237;
	//已经发送过类型信息
	const HAVE_SEND_THIS_TYPE=1238;
	//没有获取到额外的奖励
	const NOT_GET_EXTRAL_AWARD=1239;
	
	
	//用户正在睡觉
	const USER_IS_SLEEPING=1240;
	//用户没有睡觉
	const USER_NOT_SLEEP=1241;
	//时区传递错误
	const TIME_ZONE_ERROR=1242;
	//已经使用了道具
	const HAS_USE_PROPS=1243;
	//用户没有雇佣该工人
	const USER_NOT_EMPLOY_FRIEND=1244;
	//动物已经死亡
	const ANIMAL_HAS_DIE=1245;
	//动物已经在产出了
	const ANIMAL_IS_PRODUCING=1246;
	//用户正在钓鱼
	const USER_IS_FISHING=1247;
	//用户没有在钓鱼
	const USER_IS_NOT_FISHING=1248;
	//不可以给好友发送礼物
	const CANT_SEND_GIFT_TO_FRIEND=1249;
	//不可以发送礼物给自己
	const CANT_SEND_GIFT_TO_SELF=1250;
	//要送的物品的数量不对
	const GIFT_SEND_INFO_ERROR=1251;
	//正在升级
	const IS_RAISING_LEVEL=1252;
	//等级达到最大
	const LEVEL_IS_MAX=1253;
	//树木不能砍伐
	const CANT_CUT_TREE=1254;	
		//friend_gameuid 不存在
	const FRIEND_GAMEUID_NOT_EXIST=1255;
	//feed已经过期了
	const FEED_EXCEED_TIME_LIMIT=1257;
	//动物不能照看
	const ANIMAL_CANT_LOOK_AFTER = 1260;
	//已经添加过
	const HAS_ADD=1256;
	
	//下线游戏
	//标识码已经使用过
	const KEY_HAS_USED=1261;
	//标识码错误
	const KEY_ERROR=1262;
	//用户的uid和gameuid不匹配
	const UID_GAMEUID_NOT_MAPPING=1263;
	//已经添加过相应的奖励
	const HAS_ADD_SUCH_REWARD=1264;
	
	//复活节活动
	const EMPTY_EASTER_INFO=1265;
	//巨蛋敲击次数不够
	const BIG_EGG_HIT_COUNT_NOT_ENOUGH=1266;
	//已经帮助过好友
	const HAS_HELP_FRIEND=1267;
	//活动超出时间限制
	const ACTIVITY_EXCEED_TIME_LIMIT=1268;
	
	//不需要发送request
	const NO_NEED_SEND_REQUEST = 1269;
	
	//vip的等级错误
	const VIP_LEVEL_ERROR = 1270;
	
	//已经有宠物在场景中
	const HAS_PET_IN_SCREEN = 1274;
	//宠物不是在场景中的
	const PET_NOT_IN_SCENE = 1275;
	//宠物没有出走
	const PET_NOT_RUN_AWAY = 1276;
	//宠物不能召回
	const PET_CAN_NOT_CALL_BACK = 1277;
	//宠物不需要购买召回
	const PET_NOT_NEED_BUY_HELP = 1278;
	//宠物已经走失了
	const PET_HAS_RUN_AWAY = 1279;
	//宠物的体力值不够
	const PET_STRENGTH_NOT_ENOUGH = 1280;
	
	// 配置文件错误
	const CONFIG_ERROR = 1301;
	// 没有相应的模块
	const LOG_MODULE_NOT_EXIST = 1302;
	// 没有相应的平台处理类
	const PLATFORM_NOT_SUPPORT = 1303;
	
	//文字中含有敏感词
	const WORD_NOT_VALIDATE=1401;
	
	
	// 系统事件状态值
	// 系统维护中
	const SYSTEM_MAINTAIN = 2001;
	const USER_IS_BANNED = 2002;
}

class CacheKey {
	// 用户信息的缓存
	const CACHE_KEY_PLATFORM_USER_INFO = 'ck_ui_%s';
	// 用户好友的缓存
	const CACHE_KEY_PLATFORM_USER_FRIENDS = 'ck_uf_%s';
	// 用户的event log
	const CACHE_KEY_USER_EVENT_LOG = 'ck_el_%d';
	// 用户的action log
	const CACHE_KEY_USER_ACTION_LOG = 'ck_al_%d';
	// 缓存单条database定义, %s为数据库类型, %d为单条记录的id
	const CACHE_KEY_DATABASE_SINGLE_DEF = 'ck_%s_%d';
	// 缓存全部database定义, %s为数据库类型
	const CACHE_KEY_DATABASE_ALL_DEFS = 'ck_%s';
	// 测试版缓存单条database定义, %s为数据库类型, %d为单条记录的id
	const CACHE_KEY_DATABASE_SINGLE_DEF_TEST = 'ck_test_%s_%d';
	// 测试版缓存全部database定义, %s为数据库类型
	const CACHE_KEY_DATABASE_ALL_DEFS_TEST = 'ck_test_%s';
	// 判断用户是否可以发生某个操作的缓存
	const CACHE_KEY_CAN_ACTION_HAPPEN_FLAG = 'ck_ah_%d_%d';
	// 数据库中变化量的累积记录的缓存
	const CACHE_KEY_ACCUMULATION_FLAG = 'ck_acc_%s_%d';
	//好友缓存信息
	const CACHE_KEY_ACCOUNT_LIMIT = 'user_limit_account_%d';
	
}

//用户工作种类的集合
class JobConst {
	// 照看植物
	const JOB_CAREFIELD = 0;
	//照看动物
	const JOB_PETANIMAL = 1;
	// 除虫除草工
	const JOB_COLLECTWOOD = 2;
	// 收集工
	const JOB_COLLECTSTONE = 3;
	// 最大雇佣数
	const MAX_EMPLOY_NUM = 4;
	// 可用金币雇佣的数目
	const MAX_EMPLOY_NUM_BYCOIN = 2;
	// 可雇佣的job种数
	const JOB_TYPE_NUM = 4;
	// 可雇佣的时间 秒数
	const WORK_TIME = 300;
	// 用money雇佣的status
	const STATUS_MONEY=1;
	// 用coin雇佣的status
	const STATUS_COIN=2;
	//雇佣结束后的status
	const STATUS_HAS_COMPLETE=3;
}

class XmlDbType {
	const XMLDB_ITEM = 'item';
	const XMLDB_TASK = 'task';
	const XMLDB_ACHIEVE = 'achieve';
}

class KeyInLevel{
	const KEY_CROP=0;
	const KEY_ANIMAL=1;
	const KEY_JOB=2;
	const KEY_ACTION=3;
}

class ActionCode {
	// 随机事件 : 潮湿
	const ACTION_WET = 1;
	// 随机事件 : 干旱
	const ACTION_DRY = 2;
	// 随机事件 : 长草
	const ACTION_PUT_WEED = 3;
	// 随机事件 : 长虫
	const ACTION_PUT_PEST = 4;
	// 自己事件 : 翻地
	const ACTION_HOE = 5;
	// 自己事件 : 播种
	const ACTION_SEED = 6;
	// 自己事件 : 施肥
	const ACTION_FERTILIZE = 7;
	//自己温室施肥
	const ACTION_FERTILIZE_GREENHOUSE =115;
	// 自己事件 : 浇水
	const ACTION_WATER = 8;
	// 自己事件 : 除草
	const ACTION_KILL_WEED = 9;
	// 自己事件 : 除虫
	const ACTION_KILL_BAD = 10;
	// 自己事件 : 收割
	const ACTION_HARVEST = 11;
	// 自己事件 : 扩建
	const ACTION_EXTEND = 12;
	// 好友事件 : 杀虫
	const ACTION_FRIEND_KILL_BAD = 13;
	// 好友事件 : 除草
	const ACTION_FRIEND_KILL_WEED = 14;
	// 好友事件 : 放虫
	const ACTION_FRIEND_PUT_PEST = 15;
	// 好友事件 : 放草
	const ACTION_FRIEND_PUT_WEED = 16;
	// 好友事件 : 浇水
	const ACTION_FRIEND_WATER = 17;
	// 好友事件 : 偷东西
	const ACTION_FRIEND_STEAL = 18;
	// 买东西
	const ACTION_BUY = 19;
	// 卖东西
	const ACTION_SELL = 20;
	// 充值
	const ACTION_PAY = 21;
	// 邀请好友
	const ACTION_INVITE = 22;
	// 转账
	const ACTION_TRANSFER = 23;
	// 用户邀请站外好友
	const ACTION_INVITE_OUTSIDE = 24;
	//动物
	// 用户养了一个动物
	const ACTION_PUT_ANIMAL = 25;
	// 用户给动物喂食
	const ACTION_FEED_ANIMAL = 26;
	// 动物产出物品
	const ACTION_ANIMAL_PRODUCE = 27;

	// 市场价格下降
	const ACTION_PRICE_FALL = 28;
	// 物品的市场价格上升
	const ACTION_PRICE_RISED = 29;
	// 偷东西被狗抓住
	const ACTION_STEAL_CATCH = 30;
	// 邀请10个以上好友
	const ACTION_INVITE_TEN = 31;
	// 给好友送礼物
	const ACTION_SEND_GIFT = 32;
	//玩家request和gift数据库最大存储量
	const ACTION_SEND_MAX_GIFT=500;
	//去好友家拿糖果
	const ACTION_GET_SWEETS_COUNT=1;
	// 邀请1个好友
	const ACTION_INVITE_ONE = 33;
	// 邀请3个好友
	const ACTION_INVITE_THREE = 34;
	// 邀请6个好友
	const ACTION_INVITE_SIX = 35;
	// 用户给狗狗喂骨头
	const ACTION_FEED_DOG = 36;
	// 制造合成物品
	const ACTION_MANUFACTURE = 37;
	// 用户抚摸动物
	const ACTION_TOUCH = 38;
	// 用户给动物打针
	const ACTION_INJECTION = 39;
	// 用户修改装饰
	const ACTION_CHANGE_DECO = 40;
	// 用户删除土地
	const ACTION_DEL_FARM = 41;
	// 完成任务
	const ACTION_COMPLETE_TASK = 42;
	//升级技能
	const ACTION_RAISEJOBLEVEL = 43;
	//雇用好友
	const ACTION_EMPLOYFRIEND = 44;
	//购买装饰
	const ACTION_BUYDECORATION = 45;
	//添加一块耕地
	const ACTION_ADD = 46;
	//清理牧场
	const ACTION_CLEAN_RANCH = 47;
	//收获动物产出
	const ACTION_ANIMAL_HARVEST = 48;
	// 到好友农场做坏事
	const ACTION_EVENT_DO_BAD = 49;
	// 到好友农场帮忙
	const ACTION_EVENT_DO_HELP = 50;
	// 升级物品等级
	const ACTION_RAISE_ITEM_LEVEL = 51;
	// 拜访好友
	const ACTION_VISIT_FRIENDS = 52;
	// 更换角色
	const ACTION_CHANGE_CHARACTER = 53;
	// 吃食物
	const ACTION_EAT_FOOD = 54;
	//清理动物尸体
	const ACTION_CLEAN_ANIMAL_BODY=55;
	// 好友事件 : 施肥
	const ACTION_FRIEND_FERTILIZE = 56;
	// 自己事件 : 更换背景
	const ACTION_SWITCH_BACKGROUND=58;
	// 自己事件 : 更换物品
	const ACTION_SWITCH_ITEM=59;
	// 好友事件 ： 帮助好友
	const ACTION_HELP_FRIENDS=60;
	// 雇用好友 ： 雇佣果园工
	const ACTION_EMPLOY_FRIEND_FOR_CURE_FIELD = 61;
	// 雇用好友 ： 雇佣养殖工
	const ACTION_EMPLOY_FRIEND_FOR_CURE_ANIMAL = 62;
	// 雇用好友 ： 雇佣伐木工
	const ACTION_EMPLOY_FRIEND_FOR_COLLECT_WOOD = 63;
	// 雇用好友 ： 雇佣挖矿工
	const ACTION_EMPLOY_FRIEND_FOR_COLLECT_STONE = 64;
	// 结束建温室
	const ACTION_FINISH_BUILD_HOUSE = 65;
	// 更改时区
	const ACTION_CHANGE_TIME_ZONE = 66;
	// 结束雇佣
	const ACTION_COMPLETE_JOB = 67;
	//加速
	const ACTION_ACCELERETE=68;
	//开始钓鱼
	const ACTION_START_FISHING=69;
	//结束钓鱼
	const ACTION_STOP_FISHING=70;
	//结束升级机器
	const ACTION_FINISH_RAISE_MACHINE=71;
	//开始用机器进行加工
	const ACTION_PROCESS=72;
	//收获机器加工的产物
	const ACTION_HARVEST_PROCESS=73;
	//将救治植物
	const ACTION_CURE_CROP=74;
	//敲打野兽
	const ACTION_CUT_WILD_ANIMAL=75;
	//敲打野兽——熊
	const ACTION_CUT_WILD_ANIMAL_BEAR=76;
	//消灭幽灵
	const ACTION_ELIMINATION_GHOST=77;
	//获取圣诞墙礼物
	const ACTION_GET_CHRISTMAS_GIFT=78;
	//打开宝箱
	const ACTION_OPEN_BOX=79;
	//给果树施肥
	const ACTION_FERTILIZE_TREE=80;
	//照顾动物
	const ACTION_LOOKAFTERANIMAL=81;	
	//宠物狗收获动物
	const ACTION_PETLOOKAFTERANIMAL=83;
	//收获果树
	const  ACTION_HARVEST_TREE=82;
	//清除雇佣回复时间
	const ACTION_CLEAR_EMPLOY_TIME=84;
	//清除雇佣回复时间
	const ACTION_CREATE_BUILDING=85;
	//机器收获
	const ACTION_HARVEST_CROPBYVEHICLE=86;
	//机器种植
	const ACTION_PLANT_CROPBYVEHICLE=87;
	//机器犁地
	const ACTION_PLOW_CROPBYVEHICLE=88;
	//机器拾荒
	const ACTION_WILDPLANT_CROPBYVEHICLE=89;
	//砍树
	const ACTION_CUT_TREE=90;
	//收获仓库中的物品的产物
	const ACTION_ITEM_LIB_HARVEST=91;
	// 购买随机任务
	const ACTION_BUY_TASK = 92;
	// 取消任务
	const ACTION_QUIT_TASK = 93;
	//起名字
	const ACTION_SET_NAME = 94;
	//获取每日的连续登陆奖励
	const ACTION_GET_DAILY_REWARD = 95;
	//砸彩蛋活动
	const ACTION_GET_EASTER_REWARD = 96;
	//打地鼠活动
	const ACTION_CUT_MOLE=104;
	//获取砸巨蛋的奖励
	const ACTION_GET_HIT_BIG_EGG_REWARD = 97;
	//砸巨蛋
	const ACTION_HIT_BIG_EGG = 98;
	//种植大赛前200领取奖励
	const ACTION_PLANTMATCH=100;
	//愚人节使用点卡
	const ACTION_SETFOOL=101;
	//使用道具
	const ACTION_USEITEM=102;
	// 直接写消息文字的action
	const ACTION_LITERAL_EVENT = 99;
	//接受礼物
	const ACTION_ACCEPT_GIFT = 103;
	//选美大赛
	const ACTION_BEAUTY_CONTEST = 105;
	//删除磨坊
	const ACTION_DELETE_ROOM=106;
	//删除磨房配方
	const ACTION_DELETE_ROOM_ITEM=120;
	//试用技能房
	const ACTION_USE_ROOM=107;
	//收获房屋的产品
	const ACTION_HARVEST_ROOM=109;
	//合成新菜单
	const ACTION_ADD_ROOM_LIB=108;
	//玩家等级提升
	const ACTION_UPGRADE_LEVEL=110;
	//腾讯QQ的vip玩家添加每日奖励
	const ACTION_ADD_VIP_REWARD=111;
	//腾讯QQ的vip用户的新手礼包
	const ACTION_ADD_VIP_NEW_USER_REWARD=112;
	//装饰一下房屋
	const ACTION_CUSTOMIZE_HOUSE=113;
	//将装饰放到仓库
	const ACTION_ADD_DECO_LIB=114;
	//召回宠物狗
	const ACTION_CALL_BACK_PET=116;
	//宠物购买好友帮助
	const ACTION_PET_BUY_HELP=117;
	//collection兑换
	const ACTION_EXCHANGE_COLLECTION=118;
	//宠物狗玩耍
	const ACTION_PET_PLAY = 119;
	//买任务的一个条件
	const ACTION_BUY_TASK_NODE=121;
	//使用农币把玩家的作物加速至成熟
	const ACTION_FERTILIZE_TO_MATURE = 122;
	//给宠物洗澡
	const ACTION_CLEAN_PET=123;
	//添加占卜卡片
	const ACTION_ADD_HOUSE_CARD=124;
	//购买装饰好友帮助
	const ACTION_DECO_BUY_HELP=125;
	//把糖果放到装饰里
	const ACTION_ADD_DECO_SWEETS=126;
	//去好友家里拿糖果
	const ACTION_GET_FRIEND_SWEETS=127;
	//加工糖罐
	const ACTION_GET_EXCHANGEGOODS=128;
	//爱心糖兑换
	const ACTION_GET_EXCHANGE_SWEETS=129;
	//领取发放爱心糖果到一定数量的奖励
	const ACTION_GET_DECO_REWARDS=130;
	//种植玫瑰
	const ACTION_PLANT_ROSE=131;
	//获取排行榜奖励
	const ACTION_GET_RANK_REWARD=132;
	//xmas签到
	const ACTION_XMAS_CHECKIN=133;
	//删除xmas抽奖牌
	const ACTION_XMAS_REMOVE_LOTTERY=134;
	//刷新xmas抽奖牌内容
	const ACTION_XMAS_REFRESH_LOTTERY=135;
	//打猴子
	const ACTION_HIT_MONKEY=136;
	//使用雪人许愿
	const ACTION_USE_SNOWMAN_SKILL=137;
	//购买房屋的帮助
	const ACTION_HOUSE_BUY_HELP=138;
	//send虚拟礼物
	const ACTION_SEND_VIRTUAL_GIFT=139;
	//accept虚拟礼物
	const ACTION_ACCEPT_VIRTUAL_GIFT=140;
	//获取房屋礼品
	const ACTION_GET_HOUSE_REWARD=141;
	//领取feed奖励
	const ACTION_GET_FEED_REWARD = 142;
	//购买feed奖励
	const ACTION_BUY_FEED_REWARD = 143;
	//新的使用配方的接口
	const ACTION_SET_COMPOSITION_NEW = 144;
	//炸年兽的接口
	const ACTION_BOMB_MONSTER_NIAN = 145;
	//打大年兽的接口
	const ACTION_HIT_BIG_MONSTER_NIAN=146;
	//将机器放入机器工厂
	const ACTION_ADD_MACHINE_TO_FACTORY=147;
	//机器工厂机器加工
	const ACTION_PROCESS_IN_FACTORY=148;
	//机器工厂机器收获
	const ACTION_HARVEST_PROCESS_IN_FACTORY=149;
	//添加新农民币
	const ACTION_ADD_MONEY = 198;
	// 发送feed
	const ACTION_SEND_FEED = 199;
	// 发送notification
	const ACTION_SEND_NOTIFICATION = 200;
	//扩大加工机容量
	const ACTION_UPDATE_FAC_PLACE = 201;
	//切换加工机专精
	const ACTION_FAC_EXPERTISE = 202;
	//购买开箱子的条件
	const ACTION_SPE_BOX_HP = 203;
	//开始捕鱼
	const ACTION_FISH_START = 204;
	//收获鱼
	const ACTION_FISH_END  = 205;
	//购买鱼塘好友帮助
	const ACTION_FISHLAB_BUY_HELP=206;
	//购买收获鱼的过期时间
	const ACTION_CLEAN_FISH_CD=207;
	//捡起垃圾
	const ACTION_PICK_UP_RUBBISH = 208;
	//完成建造与他那个
	const ACTION_FINISH_FISH_POOL = 209;
	//将鱼放入鱼塘
	const ACTION_PUT_FISH_IN_POOL = 210;
	//扔垃圾
	const ACTION_THROW_RUB = 211;
	//清理垃圾
	const ACTION_CLEAN_RUB = 212;
	//卖鱼
	const ACTION_HARVEST_FISH = 213;
	//喂鱼
	const ACTION_FEED_FISH = 214;
	//捕病鱼
	const ACTION_HAR_WEAK_FISH = 215;
	//修建星座建筑
	const ACTION_STARSIGN_CHOOSE = 216;
	//好友帮助打开箱子任务
	const ACTION_OPEN_BOX_HELP = 217;
	//珍珠扭蛋机
	const ACTION_GET_PEARL = 218;
	//喂食鸽子
	const ACTION_FEED_PIGEON=219;
	//完成XX个订单
	const ACTION_FINISH_RANDOM_MISSION=220;
	//使用机器人技能
	const ACTION_USE_ROBOT_SKILL = 221;
	//太阳能捡太阳
	const ACTION_ROBOT_PICKUP_SUN = 222;
	//太阳能充电
	const ACTION_ROBOT_USE_SOLAR_HOUSE = 223;
	//购买填充机器人自爆空位栏
	const ACTION_ROBOT_BUY_FILL_POS = 224;
	//机器人行为 统计
	//一键施肥  
	const ACTION_ROBOT_SKILL_FERTILIZE = 225;
	//一键收获 
	const ACTION_ROBOT_SKILL_HARVEST_CROP = 226;
	//一键种植
	const ACTION_ROBOT_SKILL_PLANT = 227;
	// 一键加工  
	const ACTION_ROBOT_SKILL_FACTORY = 228;
	//自爆
	const ACTION_ROBOT_SKILL_BOOM = 229;
	//一键收获果树
	const ACTION_ROBOT_SKILL_HARVEST_TREE = 230;
	//ban user
	const ACTION_USER_BANNED = 231;
	//刷新祭坛
	const ACTION_REGETTAROTALTAR = 232;
	//祭坛兑换
	const ACTION_TRADETAROTALTAR = 233;
	//祭坛好友兑换
	const ACTION_TRADEWITHFRIEND = 234;
	//发布新的交易
	const ACTION_SETNEWTRADE = 235;
	//清理个人交易
	const ACTION_CLEANMYTRADECD = 236;
	//清理好友交易
	const ACTION_REGETFRIENDSTRADE = 237;
	
	
	public static $need_check_count=array(
		self::ACTION_FRIEND_STEAL,
		self::ACTION_HELP_FRIENDS,);
	public static $need_reduce_strength=array(
		//动物要减少体力的操作
		self::ACTION_CLEAN_ANIMAL_BODY,
		self::ACTION_FEED_ANIMAL,
		self::ACTION_INJECTION,
		self::ACTION_TOUCH,
		//植物要减少体力的操作
		self::ACTION_FERTILIZE,
		self::ACTION_KILL_BAD,
		self::ACTION_WATER,
		self::ACTION_SEED,
		self::ACTION_ADD,
		self::ACTION_HOE,
		//动物植物共有的操作
		self::ACTION_FRIEND_STEAL,
		self::ACTION_HARVEST,
		//整个游戏的操作
		self::ACTION_CLEAN_RANCH,
		self::ACTION_FEED_DOG);
	public static $need_record_count=array(
		//整个游戏
		self::ACTION_COMPLETE_TASK,
		self::ACTION_HELP_FRIENDS,
		self::ACTION_SEND_GIFT,
		//动物 
		self::ACTION_FEED_ANIMAL,
		self::ACTION_INJECTION,
		self::ACTION_TOUCH,
		//植物
		self::ACTION_FERTILIZE,
		self::ACTION_KILL_BAD,
		self::ACTION_WATER,
		self::ACTION_HOE,
		//动物植物共有
		self::ACTION_HARVEST,);
}
class ItemType{
	// 食物的Item类型
	const ITEM_TYPE_FOOD=3;
	// 动物的item类型
	const ITEM_TYPE_ANIMAL=1;
	// 养成物的产品item类型
	const ITEM_TYPE_PRODUCT=4;
	// 工具类的item类型
	const ITEM_TYPE_TOOL=2;
	// 卡片类的item类型
	const ITEM_TYPE_CARD=5;
	// 药品类的item类型
	const ITEM_TYPE_PILL=6;
	// 植物的种子类型
	const ITEM_TYPE_CROP=9;
	// 房屋类的item类型 
	const ITEM_TYPE_HOUSE=11;
	// 背景类的item类型
	const ITEM_TYPE_BACKGROUND=12;
	// 背景类的item类型
	const ITEM_TYPE_DECORATION=8;
	//技能类的job
	const ITEM_TYPE_JOB=15;
	// 狗类的item类型
	const ITEM_TYPE_DOG=17;
	// 资源类型
	const ITEM_TYPE_RESOURCE=10;
	//收藏的物品类型
	const ITEM_TYPE_COLLECTION=25;
	// 场景装饰类型
	const ITEM_TYPE_SCENEDECORATION=16;
	// 家具类型
	const ITEM_TYPE_FURNITURE=18;
	// 花的种子类型
	const ITEM_TYPE_FLOWER=19;
	//树的类型
	const ITEM_TYPE_TREE=30;
	//虚拟物品
	const ITEM_TYPE_VIRTUAL=35;
	//花的产物类型
	const ITEM_TYPE_FLOWER_PRODUCT=59;
	//作物的产物类型
	const ITEM_TYPE_CROP_PRODUCT=49;
	//果树种子的类型
	const ITEM_TYPE_FRUIT_SEED=20;
	//果树产物的类型
	const ITEM_TYPE_FRUIT_PRODUCT=60;
	//主题包系列道具
	const ITEM_TYPE_ACTIVITY_PACKAGE_POP = 29;
	//房屋地基type
	const ITEM_TYPE_BUILDING_BASE=32;
	//成就信息
	const ITEM_TYPE_ACHIEVE = 7;
	//清道夫
	const ITEM_TYPE_QINGDAOFU=41;
}
class GameConstCode{
	
	//浇水持续的时间
	const WATER_TIME=1800;
	//雇佣结束时的基本经验
	const BASE_EXP=1000;
	//雇佣结束时的基本金币
	const BASE_COINPRICE = 1000;
	//偷东西时金币最大扣除量
	const MAX_LOST = 500;
	//清扫牧场的周期（小时）
	const CLEAN_CYCLE = 4;
	//给动物打针加的生命值
	const CURE_ADDED_HEALTH = 50;
	//抚摸动物增加的亲密度值
	const PET_ANIMAL_INTIMATE = 100;
	//体力值涨满的周期(小时)
	const STRENGTH_FULL_CYCLE = 24;
	//体力值和时间的对应关系，一点体力值对应300秒
	const STRENGTH_TO_TIME = 300;
	//睡觉时的体力值和时间的对应关系，一点体力值对应270秒
	const STRENGTH_TO_TIME_SLEEP = 270;
	//不同任务的分割数字
	const DIFFERENT_TASK_AMBIT = 2000;
	//删除一块土地需要的花费coin
	const DEL_FARM_COST = 100;
	//warehouse表中数据提交数据库的COIN界限
	const WAREHOUSE_COIN_LIMIT = 5000;
	//warehouse表中数据提交数据库的count界限
	const WAREHOUSE_COUNT_LIMIT = 50;
	//用户一天的经验的增加上限
	const ONE_DAY_EXPERIENCE_LIMIT = 5000;
	//当前item_level中的最大值
	const MAX_LEVEL_IN_ITEMLEVEL = 4;
	//农场的边长
	const FARM_SIDE_LENGTH = 9;
	//平台缓存时间
	const PLATFORM_INFO_EXPIRE_TIME = 604800;
	//邮件的保存时间，单位天
	const MAIL_CONSERVE_TIME = 30;
	//动物养殖的基本数量
	const ANIMAL_BASE_COUNT = 4;
	//开垦土地的基本数量
	const FIELD_BASE_COUNT = 6;
	//活动开始时间
	const COUPON_START_TIME = 1272499200;
	//活动结束时间
	const COUPON_EXPIRE_TIME = 1273881600;
	//活动中每天每人送点卷数量
	const COUPON_FOR_ACTIVITY = 4;
	//每天每人送点卷数量
	const COUPON_PER_DAY = 2;
	//喂养动物时消耗的饲料的数量
	const ANIMAL_FERTILIZE_COUNT = 4;
	//通过feed进入的好友获得的经验值
	const FEED_EXPERIENCE=5;
	//通过feed进入的好友获得的金币值
	const FEED_COIN=20;
	//最短的枯萎时间，0.5*60*60,半小时
	const MIN_WITHERED_TIME=1800;
	//免费礼物的最大赠送数量
	const MAX_SEND_FREE_GIFT_COUNT=30;
	//添加额外的体力值的概率0.1
	const ADD_STRENGTH_PROPERTY=0.1;
	//产物双倍的概率0.05
	const ADD_TWINCE_PRODUCTION=0.05;
	//收获的时候爆出黑宝石的概率
	const ADD_HARVEST_CROP=0.06;
	//收获温室爆出黑宝石的概率
	const ADD_HARVEST_GREENHOUSE=0.07;
	//帮助好友作物的时候爆出黑宝石
	const ADD_FRIEND_CROP=0.09;
	
	//体力值上限双倍的截止时间
	const TWINCE_STRENGTH_TIME_FLAG="2011-2-14 00:00:00";
	
}
class InitUser{
	public static $account_arr = array(	'exp' => 0,
										'gem' => 50,
										'coin' => 10000,
										'love' => 0,
										'extend' => 0,
										'crop_extend' => 0,
										'sex' => 0,
										'title' => "",
	'skill' => "2|5"
	
										);
	public static $own_arr = array(
		array('item_id'=>10001,'count'=>5),
		array('item_id'=>10002,'count'=>5),
		array('item_id'=>10003,'count'=>5),
		array('item_id'=>14000,'count'=>1),
		array('item_id'=>20001,'count'=>10)
		);
	
	public static $new_field = array(
		array('data_id'=>"1001",'positiony'=>8,'positionx'=>6,"item_id"=>10000,"output"=>2,"plant_time"=>0,"status"=>0),
		array('data_id'=>"1002",'positiony'=>8,'positionx'=>8,"item_id"=>10000,"output"=>2,"plant_time"=>0,"status"=>0),
		array('data_id'=>"1003",'positiony'=>8,'positionx'=>10,"item_id"=>0,"output"=>0,"plant_time"=>0,"status"=>0),
		array('data_id'=>"1004",'positiony'=>10,'positionx'=>6,"item_id"=>10000,"output"=>2,"plant_time"=>0,"status"=>0),
		array('data_id'=>"1005",'positiony'=>10,'positionx'=>8,"item_id"=>10000,"output"=>2,"plant_time"=>0,"status"=>0),
		array('data_id'=>"1006",'positiony'=>10,'positionx'=>10,"item_id"=>0,"output"=>0,"plant_time"=>0,"status"=>0)
				
	);
	public static $new_deco = array(
		array('data_id'=>"1001",'positiony'=>2,'positionx'=>6,"item_id"=>50001),
		array('data_id'=>"1002",'positiony'=>7,'positionx'=>8,"item_id"=>50009),
		array('data_id'=>"1003",'positiony'=>6,'positionx'=>8,"item_id"=>50009),
		array('data_id'=>"1004",'positiony'=>6,'positionx'=>1,"item_id"=>50010),
		array('data_id'=>"1005",'positiony'=>4,'positionx'=>2,"item_id"=>50013),
		array('data_id'=>"1006",'positiony'=>12,'positionx'=>4,"item_id"=>50016),
		array('data_id'=>"1007",'positiony'=>0,'positionx'=>12,"item_id"=>50014),
		array('data_id'=>"1008",'positiony'=>1,'positionx'=>1,"item_id"=>50015)
	);
	
	public static $treasure_activity = array(
		"littleFarmGem"	=>array(array("id"=>"coin","count"=>500),array("id"=>"20001","count"=>20),array("id"=>"14001","count"=>1)),
		"largeFarmGem"	=>array(array("id"=>"coin","count"=>3000),array("id"=>"20001","count"=>100),array("id"=>"14005","count"=>1)),
//		"time"	=> "1402980329"
	);
	
	public static $new_fac = array(
		'expand'=>0,
		'workTime'=>0
	);
	
}
class GameModelConfig{
	const CHANGENAME_COST = 2;
	const CHANGENAME_SEX = 2;
	
	const TASK_MAX_COIN = 1;
	const TASK_MIN_COIN = 1;
	const TASK_MAX_EXP = 1;
	const TASK_MIN_EXP = 1;
	const TASK_NPC_RANDMAX = 2;
	const TASK_NPC_RANDMIN = 1;
	const TASK_EXPIRE_TIME = 43200;
	const TASK_CD_TIME = 28800;
	
	const SKILL_CD  = 28800;
	
	const FACTORY_TILES = 5;
	
}
class MethodType {
	//表示物品的变化方式
	const METHOD_APPEND = 1;
	const METHOD_UPDATE = 2;
	const METHOD_ADD = 3;
	const METHOD_SUB = 4;
	const METHOD_DEL = 5;
	//购买物品时的方式
	const METHOD_COIN = 1;
	const METHOD_MONEY = 2;
	//field 操作类型
	const ADD_FIELD = 1;
	const PLANT = 2;
	const SPEED = 3;
	const HARVEST = 4;
	const MOVE = 5;
	const SELL = 6;
	const BUILD = 7;
	const HARVESTANIMAL = 8;
	const FEEDANIMAL = 9;
	
	//task类型
	const TASK_NONPC = 0;
	const TASK_MALENPC = 1;
	const TASK_FEMALENPC = 2;
	
	//TASK 奖励类型
	const TASK_REWARD_COIN = 1;
	const TASK_REWARD_EXP = 2;
	const TASK_REWARD_LOVE = 3;
	
	//MESSAGE类型
	const MESSTYPE_MES = 0;
	const MESSTYPE_INVITE = 1;
	const MESSTYPE_HELP = 2;
	const MESSTYPE_ORDER = 3;
	const MESSTYPE_ROBBER = 4;
}

class StaticFunction{
	public static function expToGrade($exp){
        return intval (sqrt($exp/10));
    }
    public static function gradeToExp($grade){
        return intval(pow($grade,2)*10);
    }
	public static function gradeToLove($grade){
        return intval(pow($grade+1,2)*10 - pow($grade,2)*10);
    }
    public static function getStrengthLimit($level=0,$increace=0){
    	return intval($level/2)+$increace + 15;
    }
    public static $wildRewardRate =  array(
    					array(70,30),
				    	array(60,30,10),
				    	array(40,40,20),
				    	array(30,30,20,10,10),
				    	array(10,20,30,20,20)
				    	);
	 public static $wildRewards =  array( 
	 					array('coin'=>1200,'coin'=>800,'exp'=>150,'14000'=>1,'20001'=>1,'50009'=>1,'50010'=>1),
				    	array('coin'=>2500,'coin'=>1500,'exp'=>100,'14000'=>1,'14001'=>1,'20001'=>2,'50010'=>1,'50011'=>1),
				    	array('coin'=>3000,'coin'=>6000,'14000'=>1,'14001'=>1,'14002'=>1,'20001'=>3,'50010'=>1,'50011'=>1,'50012'=>1),
				    	array('14000'=>1,'14001'=>1,'14002'=>1,'14003'=>1,'14004'=>1,'20001'=>8,'50011'=>1,'50012'=>1),
				    	array('14006'=>1,'14005'=>1,'14004'=>1,'20001'=>30,'50012'=>1)
			    	);
				    	
	public static function getWildReward($step){
//    	$rate = StaticFunction::$wildRewardRate[$step];
//    	$key = StaticFunction::getOneByRate($rate);
    	
		$rewards = StaticFunction::$wildRewards[$step];
		$r_key = array_rand($rewards,1);
		
		return array("id"=>$r_key,"count"=>$rewards[$r_key]);
    }
    /**
     * 获取一系列rate中的一个而且必须要出现的一个
     *	rate必须是整数
     * @param $awards_rate array($k=>$rate)
     * @return unknown
     */
	public static function getOneByRate($awards_rate){
		$total_rand=array_sum($awards_rate);
		$rand_key=mt_rand(0,$total_rand);
		foreach ($awards_rate as $k=>$rate){
			if ($rand_key<=$rate){
				return $k;
			}else {
				$rand_key-=$rate;
			}
		}
	}
}
?>