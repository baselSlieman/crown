<?php

namespace App\Livewire;

use App\Models\Chat;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Affiliates extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = "bootstrap";
    public $search = '';

    public function render()
    {
        if (session('locale') !== null) {
            App::setLocale(session('locale'));
        }


        $sql = "
SELECT chat_id,chat_username,follower_chats_count,sumaff From
(SELECT
    parent.id as chat_id,
 	parent.username as chat_username,
    COUNT(child.id) as follower_chats_count
FROM chats as parent
LEFT JOIN chats as child ON child.affiliate_code = CAST(parent.id AS CHAR)
GROUP BY parent.id,parent.username
HAVING COUNT(child.id) > 0
ORDER BY follower_chats_count DESC) AS basel
LEFT JOIN (
SELECT  chats.id as chatid, COALESCE(SUM(affiliates.affiliate_amount),0) as sumaff
from chats,affiliates
where chats.id=affiliates.chat_id
GROUP BY chats.id) AS ali
ON basel.chat_id = ali.chatid
";

        // $results = DB::select($sql);
        if ($this->search != '') {
            $sql .= " WHERE chat_id LIKE :chat_id";
            $results = DB::select($sql, ['chat_id' => "%" . $this->search . "%"]);
        } else {
            $results = DB::select($sql);
        }
        return view('livewire.affiliates', compact('results'))->layout('admin.layouts.livewire');
    }
}
