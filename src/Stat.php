<?php
	
namespace StatIg\Stat;

use function StatIg\Storage\getUserData;
use Illuminate\Support\Collection;

function getUserStat(string $username):array
{
	$data = getUserData($username);
	if ($data === null) {
		throw new \Exception("Data not found for $username");
	}
	
	return getStatFromUserData($data);
}

function getStatFromUserData(\stdClass $data): array
{
	$stat = [];
	$followers = $data->graphql->user->edge_followed_by->count ?? null;
	$stat['followers'] = $followers;
	$stat['posts'] = [];
	
	$postNodes = $data
				->graphql
				->user
				->edge_owner_to_timeline_media
				->edges;
	
	foreach ($postNodes as $nodeData) {
		$node = $nodeData->node;
		$comments = $node->edge_media_to_comment->count;
		$likes = $node->edge_liked_by->count;
		$engagements = $comments + $likes;
		$er = null;
		if ($followers > 0) {
			$er = round ($engagements / $followers * 100);
		}
		
		$post = [
			'comments' => $comments,
			'likes' => $likes,
			'engagements' => $engagements,
			'er' => $er,
			'url' => "https://www.instagram.com/p/{$node->shortcode}"
			
		
		];
		$stat['posts'][] = $post;
	}
	
	if ($followers > 0) {
		
	$stat['avgEr'] = getAverageEngagementRate($stat['posts'], $followers);
	
	}
	
	$stat['mostLikedPost'] = findTopPost($stat['posts'], 'Likes');
	$stat['mostCommentedPost'] = findTopPost($stat['posts'], 'comments');
	$stat['topEr'] = findTopPost($stat['posts'], 'er');
	
	return $stat;
}

function getAverageEngagementRate(array $posts, int $followers): float
{	
	$totalEngs = 0;
	foreach ($posts as $post) {
	
		$totalEngs += $post['engagements'];
	
	}
	 /*
	$totalEngs = array_reduce(
		$posts,
		fn($engs, $post) => $engs + $post['engagements'],
		0);
		
	*/
		
	return round($totalEngs / $followers / count($posts) * 100, 2);
}


function findTopPost(array $posts, $property): ?array
{
	
	$collection = new Collection($posts);
	
	return $collection->sortByDesc($property)
	->first();
	
}