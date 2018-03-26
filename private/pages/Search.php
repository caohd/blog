<?php

class Search
{
    public function init()
    {
        $input = Input::getInstance();
        $keys = $input->get('search');
        $data = [];

        $data['nick'] = User::getCurUser()['nickname'];
        $data['logo'] = User::getCurUser()['logo'];
        if (empty($keys)) {
            echo packData('success', 1, $data, '没有关键词');
            return;
        }
        $keys = explode(' ', $keys);
        // 只有一个关键词的时候才搜索用户
        if (1 === count($keys)) {
            $sql = <<<SQL
            SELECT b_user.id, logo, nickname, username, b_user.brief, b_relationship.id AS isFriend
            FROM b_user 
            LEFT JOIN b_relationship ON b_relationship.uid1=? AND b_relationship.uid2=b_user.id AND b_relationship.f1to2=1
            WHERE (username LIKE ? OR nickname  LIKE ?) AND b_user.id != ?
SQL;
            $db = DB::getInstance(getConfig('db'));
            $db->prepareAndExec($sql, [User::getCurUser()['id'] ?? 0, '%'.$keys[0].'%', '%'.$keys[0].'%', User::getCurUser()['id']]);
            $data['users'] = $db->getResultSet();
        }

        $am = $this->getData(20, $keys);
        $data['articles'] = $am['articles'];
        $data['moments'] = $am['moments'];

        echo packData('success', 0, $data, '是的');

    }

    public function getData(int $max, $keys) : array
    {
        $db = DB::getInstance(getConfig('db'));
        $params = [User::getCurUser()['id'] ?? 0, User::getCurUser()['id'] ?? 0];
        $alike = '';
        $mlike = '';
        foreach ($keys as $key) {
            $alike = "b_article.title LIKE ? OR ";
            $mlike = "b_moment.content LIKE ? OR ";
            $params[] = '%'.$key.'%';
        }
        $alike = rtrim($alike, 'OR ');
        $mlike = rtrim($mlike, 'OR ');
        $asql =
            <<< SQL
            SELECT 
            b_article.*, b_user.nickname, b_user.logo, b_user.username as user, b_like.id as `like`, b_relationship.id AS isFriend
            FROM b_article 
            LEFT JOIN b_user ON b_user.id=b_article.uid
            LEFT JOIN b_like ON b_like.aid=b_article.id AND b_like.uid=? AND b_like.typeof=1
            LEFT JOIN b_relationship ON b_relationship.uid1=? AND b_relationship.uid2=b_user.id AND b_relationship.f1to2=1
            WHERE b_article.flag=0 AND {$alike}
            ORDER BY time DESC 
            LIMIT $max
SQL;

        $db->prepareAndExec($asql, $params);
        $articles = $db->getResultSet();

        $msql =
            <<< SQL
            SELECT 
            b_moment.*, b_user.nickname,b_user.logo, b_user.username AS user, b_mpicture.src, b_like.id as `like`, b_relationship.id AS isFriend
            FROM b_moment 
            LEFT JOIN b_user ON b_user.id=b_moment.uid 
            LEFT JOIN b_mpicture ON b_mpicture.aid=b_moment.id
            LEFT JOIN b_like ON b_like.uid=? AND b_like.aid=b_moment.id AND b_like.typeof=3
            LEFT JOIN b_relationship ON b_relationship.uid1=? AND b_relationship.uid2=b_user.id AND b_relationship.f1to2=1
            WHERE {$mlike}
            ORDER BY time DESC 
            LIMIT $max
SQL;
        $db->prepareAndExec($msql, $params);
        $moments = $db->getResultSet();
        return ['articles' => $articles, 'moments' => $moments];
    }
}