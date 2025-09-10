<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * @copyright: ©2020 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\api\index;

use app\bootstrap\Pagination;
use app\bootstrap\response\ApiCode;
use app\models\AvData;
use app\models\Model;
use app\models\VisualImage;
use app\models\VisualVideo;
use app\models\User;
use yii\db\Expression;
use yii\db\Query;

class SquareForm extends Model
{
    public $page;
    public $url;
    public $type;
    public $limit;

    public function rules()
    {
        return [
            [['page', 'limit'], 'integer'],
            [['url', 'type'], 'string'],
            [['limit'], 'default', 'value' => 20],
        ];
    }

    public function get()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        try {
            // 构建图片查询
            $imageQuery = VisualImage::find()
                ->select([
                    'id',
                    'user_id',
                    'type',
                    'prompt',
                    'data',
                    'image_url as url',
                    'is_home',
                    'created_at',
                    'visual_type' => new Expression('\'image\''),
                ])
                ->where([
                    'mall_id' => \Yii::$app->mall->id,
                    'is_admin_public' => 1,
                    'is_user_public' => 1,
                    'is_delete' => 0
                ]);

            // 构建视频查询
            $videoQuery = VisualVideo::find()
                ->select([
                    'id',
                    'user_id',
                    'type',
                    'prompt',
                    'data',
                    'video_url as url',
                    'is_home',
                    'created_at',
                    'visual_type' => new Expression('\'video\''),
                ])
                ->where([
                    'mall_id' => \Yii::$app->mall->id,
                    'is_admin_public' => 1,
                    'is_user_public' => 1,
                    'is_delete' => 0,
                    'status' => 2  // 只查询处理成功的视频
                ]);

            if(!$this->type) {
                // 使用UNION合并查询
                $unionQuery = $imageQuery->union($videoQuery, true);
            }else{
                if($this->type == 'image'){
                    $unionQuery = $imageQuery;
                }else{
                    $unionQuery = $videoQuery;
                }
            }

            // 创建主查询来处理排序和分页
            $query = (new Query())
                ->from(['union_data' => $unionQuery])
                ->orderBy(new Expression('RAND()'));

            // 分页查询
            $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => $this->limit, 'page' => $this->page - 1]);
            $list = $query->limit($pagination->limit)->offset($pagination->offset)->all();

            // 处理数据，添加用户信息
            $userIds = array_column($list, 'user_id');
            $users = [];
            if (!empty($userIds)) {
                $userList = User::find()
                    ->select(['id', 'nickname'])
                    ->where(['id' => $userIds])
                    ->with("userInfo")
                    ->asArray()
                    ->all();
                $users = array_column($userList, null, 'id');
            }

            // 格式化数据
            $offset = $pagination->offset;
            foreach ($list as &$item) {
                $offset++;
                $item['pid'] = $offset;
                $item['user_nickname'] = $users[$item['user_id']]['nickname'] ?? '--';
                $item['user_avatar'] = $users[$item['user_id']]['userInfo']['avatar'] ?? '';
                $data = json_decode($item['data'], true);
                $item['data'] = [];
                if($item['visual_type'] == 'image'){
                    if(!isset($data['size'])){
                        $data['size'] = implode('x', [
                            $data['output_width'] ?? $data['width'],
                            $data['output_height'] ?? $data['height'],
                        ]);
                    }
                    $item['data']['size'] = $data['size'];
                }else{
                    $item['data']['size'] = $data['resolution'] ?? '';
                }
            }

            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '成功',
                'data' => [
                    'list' => $list,
                    'pagination' => $pagination,
                ],
            ];
        } catch (\Exception $exception) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $exception->getMessage(),
            ];
        }
    }

    public function down()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        // 发送文件到客户端
        return \Yii::$app->response->sendFile((new AvData())->localFile($this->url));
    }
}
