<?php

namespace App\CPU;

use App\Models\Interest;
use App\Models\User;
use App\Models\UserInterest;
use App\Models\UserMatch;
use Carbon\Carbon;
use Google\Cloud\Firestore\FieldValue;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Firestore;
use Mockery\Exception;

class FireStoreHelper
{
    private Firestore $firestore;

    public function __construct(Firestore $firestore)
    {
        $this->firestore = $firestore;
    }

    public function addUserInfo($user): void
    {
        try {
            $now = Carbon::now();
            $userData = array();
            $userData["id"] = $user->id;
            $userData["name"] = $user->name;
            $userData["image"] = $user->image;
            $userData["bio"] = $user->bio;
            $userData["gender"] = $user->gender;
            $userData["occupation"] = $user->occupation;

            $docRefL1 = $this->firestore->database()->collection(env('FIRESTORE_USER_COLLECTION'))->document($user->id);
            $docRefL1->set($userData);

        } catch (Exception $exception) {

        }
    }

    public function updateUserInfo($user): void
    {
        try {
            $now = Carbon::now();
            $userData = array();
            $userData["id"] = $user->id;
            $userData["name"] = $user->name;
            $userData["image"] = $user->image;
            $userData["bio"] = $user->bio;
            $userData["gender"] = $user->gender;
            $userData["occupation"] = $user->occupation;

            $docRefL1 = $this->firestore->database()->collection(env('FIRESTORE_USER_COLLECTION'))->document($user->id);
            $docRefL1->set($userData);

        } catch (Exception $exception) {

        }
    }

    public function addMessageToCollection($from, $to, $message): void
    {
        try {
            $now = Carbon::now();
            $messageId = (string)Carbon::now()->format('Uu');
            $collectionPathL1 = env('FIRESTORE_CHAT_COLLECTION');
            $channel = $to->id < $from->id ? $to->id . '_' . $from->id : $from->id . '_' . $to->id;
            $messageCollectionPath = env('FIRESTORE_CHAT_COLLECTION') . '/' . $channel . '/messages';


            // CHANNEL DATA
            $channelData = array();
            $channelData['doc_id'] = $channel;
            $channelData["created_at"] = $now;
            $channelData["created_by"] = $from->id;
            $channelData["message"] = $message;
            $channelData["message_time"] = $now;
            $channelData['message_delete_status'] = 0;
            $channelData['message_id'] = $messageId;
            $channelData["sender"] = $this->getSenderInfo($from);
            $channelData["sender_id"] = $from->id;
            $channelData["members"][] = $this->getChannelMemberInfo($from, 0);
            $channelData["members"][] = $this->getChannelMemberInfo($to, 1);
            $channelData["thread_name"] = null;
            $channelData["thread_icon"] = null;
            $channelData["thread_member_ids"] = array($from->id, $to->id);
            //CREATE AND SAVE CHANNEL DATA
            $docRefL1 = $this->firestore->database()->collection($collectionPathL1)->document($channel);
            $docRefL1->set($channelData);


            // MESSAGE DATA
            if($message!= ''){
                $messageData['created_at'] = $now;
                $messageData['medias'] = array();
                $messageData['text'] = $message;
                $messageData['message_delete_status'] = 0;
                $messageData["sender_id"] = $from->id;
                $messageData['sender'] = $this->getSenderInfo($from);
                $messageData['receivers'][] = $this->getReceiverInfo($to, $now);
                $messageData['seen_by'] = array();
                $messageData['reaction'] = array();
                //SAVE MESSAGE DATA
                $docRef = $this->firestore->database()->collection($messageCollectionPath)->document($messageId);
                $docRef->set($messageData);
            }

        } catch (Exception $exception) {

        }

    }

    public function createOnlyChannel($from, $to){
        $doc_id = $this->createUserToUserChannel([$from->id, $to->id]);

        $collection = $this->firestore->database()->collection(env('FIRESTORE_CHAT_COLLECTION'));
        $document = $collection->document($doc_id)->snapshot();
        $counter = 0;
        if(!$document->exists()){
            echo $counter++ . ': '.$doc_id . " document NOT Exist <br>";


            $now = Carbon::now();
            $messageId = (string)Carbon::now()->format('Uu');
            $collectionPathL1 = env('FIRESTORE_CHAT_COLLECTION');
            $channel = $to->id < $from->id ? $to->id . '_' . $from->id : $from->id . '_' . $to->id;
            $messageCollectionPath = env('FIRESTORE_CHAT_COLLECTION') . '/' . $channel . '/messages';


            // CHANNEL DATA
            $channelData = array();
            $channelData['doc_id'] = $channel;
            $channelData["created_at"] = $now;
            $channelData["created_by"] = $from->id;
            $channelData["message"] = '';
            $channelData["message_time"] = $now;
            $channelData['message_delete_status'] = 0;
            $channelData['message_id'] = null;
            $channelData["sender"] = $this->getSenderInfo($from);
            $channelData["sender_id"] = null;
            $channelData["members"][] = $this->getChannelMemberInfo($from, 0);
            $channelData["members"][] = $this->getChannelMemberInfo($to, 1);
            $channelData["thread_name"] = null;
            $channelData["thread_icon"] = null;
            $channelData["thread_member_ids"] = array($from->id, $to->id);
            //CREATE AND SAVE CHANNEL DATA
            $docRefL1 = $this->firestore->database()->collection($collectionPathL1)->document($channel);
            $docRefL1->set($channelData);

        }else{
            echo $doc_id . " document Exist <br>";
        }
    }


    public function deleteMessageFromCollection($message_id)
    {
        // TODO: Delete message need to be implemented

    }

    public function deleteDocument($doc_id)
    {
        try {

            // Specify the collection and document ID to delete
            $collectionPathL1 = env('FIRESTORE_CHAT_COLLECTION');
            // Reference to the document
            $documentReference = $this->firestore->database()->collection($collectionPathL1)->document($doc_id);
            // Delete the document
            $documentReference->delete();

            $messageCollection = $documentReference->collection('messages');
            $messageDocuments = $messageCollection->documents();
            foreach ($messageDocuments as $messageDocument) {
                $messageDocument->reference()->delete();
            }
            return true;
        } catch (Exception $exception) {
            Log::info(["message" => $exception->getMessage()]);
        }
    }

    public function deleteOnlyChannel($doc_id)
    {
        try {

            $collectionPathL1 = env('FIRESTORE_CHAT_COLLECTION');
            $documentReference = $this->firestore->database()->collection($collectionPathL1)->document($doc_id);
            $documentReference->delete();
            return true;

        } catch (Exception $exception) {
            Log::info(["message" => $exception->getMessage()]);
        }
    }

    public function addGroupChannelCollection($created_by, $sender_info, $channel_name, $group_members, $thread_member_ids, $thread_icon, $message, $receivers, $now): void
    {
        try {

            $channelId = (string)Carbon::now()->format('Uu');
            $messageId = (string)Carbon::now()->format('Uu');
            $collectionPathL1 = env('FIRESTORE_CHAT_COLLECTION');
            $channel = preg_replace('/-+/', '-', preg_replace('/[^A-Za-z0-9\-+]/', '-', $channel_name)) . '-' . $channelId;
            $messageCollectionPath = env('FIRESTORE_CHAT_COLLECTION') . '/' . $channel . '/messages';


            // CHANNEL DATA
            $channelData = array();
            $channelData["created_at"] = $now;
            $channelData["doc_id"] = $channel;
            $channelData["created_by"] = $created_by;
            $channelData["message"] = $message;
            $channelData["message_time"] = $now;
            $channelData['message_delete_status'] = 0;
            $channelData['message_id'] = $messageId;
            $channelData["sender_id"] = $sender_info['id'];
            $channelData["sender"] = $sender_info;
            $channelData["members"] = $group_members;
            $channelData["thread_name"] = $channel_name;
            $channelData["thread_icon"] = $thread_icon;
            $channelData["thread_member_ids"] = $thread_member_ids;
            $channelData["seen_by"] = array();
            //CREATE AND SAVE CHANNEL DATA
            $docRefL1 = $this->firestore->database()->collection($collectionPathL1)->document($channel);
            $docRefL1->set($channelData);


            // MESSAGE DATA
            $messageData['created_at'] = $now;
            $messageData['medias'] = array();
            $messageData['text'] = $message;
            $messageData['message_delete_status'] = 0;
            $messageData['sender'] = $sender_info;
            $messageData["sender_id"] = $sender_info['id'];
            $messageData['receivers'] = $receivers;
            $messageData['seen_by'] = array();
            $messageData['reaction'] = array();
            //SAVE MESSAGE DATA
            $docRef = $this->firestore->database()->collection($messageCollectionPath)->document($messageId);
            $docRef->set($messageData);

        } catch (Exception $exception) {

        }

    }

    public function getSenderInfo($user)
    {
        return array(
            "id" => $user->id,
            "name" => $user->name,
            "image" => $user->image,
        );
    }

    public function getReceiverInfo($user, $now)
    {
        return array(
            "id" => $user->id,
            "delivered_at" => $now,
            "read_at" => null,
        );
    }

    public function getChannelMemberInfo($user, $unreadCount)
    {
        return array(
            "id" => $user->id,
            "name" => $user->name ? $user->name : 'Unknown',
            "bio" => $user->bio ? $user->bio : "Not given",
            "Gender" => $user->gender ? $user->gender : 'Unknown',
            "image" => $user->image ? $user->image : null,
            "unread_count" => $unreadCount,
            "is_verified" => ($user->is_verified==1)?true:false,
        );
    }

    public function manipulateFirestore()
    {
        $usersRef = $this->firestore->database()->collection(env('FIRESTORE_CHAT_COLLECTION'));
        $snapshots = $usersRef->where('created_by', 'in', [1])->documents();
        foreach ($snapshots as $channel) {
            dd($channel);
//            $subcollectionDocRef = $channel->reference()->collection('messages');
//            $messages = $subcollectionDocRef->documents();
//            foreach ($messages as $message){
//                $message->reference()->delete();
//            }
//            $channel->reference()->delete();


//            $threadMemberIds = $user['thread_member_ids'];
//            dump($threadMemberIds);
//            if (!is_array($threadMemberIds)) {
//                $threadMemberIds = [$threadMemberIds];
//            }

//            $userRef = $this->firestore->database()->collection(env('FIRESTORE_USER_COLLECTION'));
//            $users = $userRef->where('id', 'in',  $threadMemberIds)->documents();
//            foreach ($users as $userInfo){
//                dump("111");
//                dump($userInfo['id']);
//            }
//            dd($users);


//            $messagesSnapshot = $messagesRef->documents();
//            echo "Messages";
//            foreach ($messagesSnapshot as $message){
//                dump($message['text']);
//                dump(Carbon::parse($message['created_at'])->format('Y-m-d h:i:s A'));
//            }
//            dump($channel['created_at']);
//            dump($channel['created_by']);
//            dump($user['members']);
//            dump("thread_member_ids: ", $user['thread_member_ids']);
//            dump($user['message']);
        }
        return true;
    }

    public function updateBlockUser($block_ids)
    {
        $doc_id = $this->createUserToUserChannel($block_ids);
        try {
            $channelData = array();
            $channelData["block_ids"] = $block_ids;
            $docRefL1 = $this->firestore->database()->collection(env('FIRESTORE_CHAT_COLLECTION'))->document($doc_id);
            $docRefL1->set($channelData, ['merge' => true]);
        } catch (\Exception $exception) {
            Log::info(["message" => $exception->getMessage()]);
        }
        return true;
    }

    public function updateUnBlockUser($unblock_ids)
    {
        $doc_id = $this->createUserToUserChannel($unblock_ids);
        try {
            $channelData = array();
            $channelData["block_ids"] = [];
            $docRefL1 = $this->firestore->database()->collection(env('FIRESTORE_CHAT_COLLECTION'))->document($doc_id);
            $docRefL1->set($channelData, ['merge' => true]);
        } catch (\Exception $exception) {
            Log::info(["message" => $exception->getMessage()]);
        }
        return true;
    }

    public function createUserToUserChannel($ids)
    {
        if ($ids[0] < $ids[1]) {
            return $ids[0] . '_' . $ids[1];
        } else {
            return $ids[1] . '_' . $ids[0];
        }
    }

    public function deleteWyzrBotChannels()
    {
        $usersRef = $this->firestore->database()->collection(env('FIRESTORE_CHAT_COLLECTION'));
        $snapshots = $usersRef->where('created_by', 'in', [1])->documents();
        foreach ($snapshots as $channel) {

            $subcollectionDocRef = $channel->reference()->collection('messages');
            $messages = $subcollectionDocRef->documents();
            foreach ($messages as $message) {
                $message->reference()->delete();
            }

            $channel->reference()->delete();
        }
        return true;
    }

    public function updateFirestoreMemberInfo($user_id)
    {
        $user_info = User::where('id', $user_id)->first();
        $chatRef = $this->firestore->database()->collection(env('FIRESTORE_CHAT_COLLECTION'));
        $snapshots = $chatRef->where('thread_member_ids', 'array-contains', $user_id)->documents();
        foreach ($snapshots as $snapshot) {
            $members = $snapshot->get('members');
            foreach ($members as &$member) {
                if ($member['id'] == $user_id) {
                    $member['name'] = $user_info->name;
                    $member['bio'] = $user_info->bio;
                    $member['email'] = $user_info->email;
                    $member['image'] = $user_info->image;
                    $member['gender'] = $user_info->gender;
                }
            }
            $docRef = $snapshot->reference();
            $docRef->set(['members'=>$members], ['merge' => true]);
        }
        return true;
    }

    public function updateChannel()
    {
        $usersRef = $this->firestore->database()->collection(env('FIRESTORE_CHAT_COLLECTION'));
        $snapshots = $usersRef->where('created_by', '!=', 1)->documents();
        $counter = 0;
        foreach ($snapshots as $channel) {
            $doc_id = '';
            $members = $channel->get('members');
            if(array_key_exists('doc_id', $channel->data())){
                $doc_id = $channel->get('doc_id');
            } else {
                $doc_id = $channel->reference()->id();
            }

            $user_matched = UserMatch::where(function ($q) use($members){
                $q->where(function ($q) use ($members) {
                    $q->where('user_one', $members[0]['id']);
                    $q->where('user_two', $members[1]['id']);
                })->orWhere(function ($q) use ($members) {
                    $q->where('user_one', $members[1]['id']);
                    $q->where('user_two', $members[0]['id']);
                });
            })->where('is_match_completed', 1)->first();

            if (!$user_matched) {
                $channel->reference()->delete();
                echo $counter++ . ':- ' . @$user_matched->id . 'Match not found '. $channel->get('created_by') . ' channel ID: ' . $channel->reference()->id() . 'DOC ID: '.$doc_id . '<br>' ;
            } else {
                // TODO do some upgrade in channel
                // echo $counter++ . ':- ' . @$user_matched->id . 'Match found '. $channel->get('created_by') . ' channel ID: ' . $channel->reference()->id() . 'DOC ID: '.$doc_id . '<br>' ;

            }
        }
        return true;
    }

}
