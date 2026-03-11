<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chatbot;
use App\Models\ChatbotEmbedding;
use App\Models\ChatbotKnowledgebase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class ExternalChatbotKnowledgeBaseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ChatbotEmbedding::where('user_id', auth()->user()->id)->where('status', 'completed')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('actions', function($row){
                    $actionBtn = '<div>      
                                    <a href="'. route("user.extension.chatbot.knowledge.edit", $row["id"] ). '"><i class="fa fa-edit table-action-buttons view-action-button" title="'. __('Edit Knowledge Base') .'"></i></a>   
                                    <a href="'. route("user.extension.chatbot.knowledge.manage", $row["id"] ). '"><i class="fa fa-link table-action-buttons view-action-button" title="'. __('Manage Chatbots') .'"></i></a>   
                                    <a class="delete" id="'. $row["id"] .'" href="#"><i class="fa-solid fa-trash-xmark table-action-buttons delete-action-button" title="'. __('Delete Knowledge Base') .'"></i></a> 
                                </div>';
                    
                    return $actionBtn;
                })
                ->addColumn('type', function($row){
                    switch ($row['type']) {
                        case 'url':
                            $type = __('Website URL');
                            break;
                        case 'text':
                            $type = __('Text');
                            break;
                        case 'qa':
                            $type = __('Q&A');
                            break;
                        case 'pdf':
                            $type = __('PDF Document');
                            break;
                        case 'youtube':
                            $type = __('Youtube Video');
                            break;
                    }
                    $type = '<span class="font-weight-bold">'. $type.'</span>';
                    return $type;
                }) 
                ->addColumn('custom-title', function($row){
                        $custom = '<span>'.Str::limit(ucfirst($row['title']), 100).'</span>';
                        return $custom;
                    })
                ->addColumn('custom-status', function($row){
                        $custom_status = '<span class="cell-box payment-'.strtolower($row["status"]).'">'.ucfirst($row["status"]).'</span>';
                        return $custom_status;
                    })
                ->addColumn('trained', function($row){
                        $created_on = '<span>'.date_format($row["trained_at"], 'd/m/Y').'</span>';
                        return $created_on;
                    })
                ->rawColumns(['actions', 'type', 'trained', 'custom-status'])
                ->make(true);
                    
        }
        
        return view('user.external_chatbot.knowledge.index');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $embedding = ChatbotEmbedding::where('id', $id)->where('user_id', auth()->user()->id)->firstOrFail();
        
        return view('user.external_chatbot.knowledge.edit', compact('embedding'));
    }

    public function update(Request $request, $id)
    {
        $embedding = ChatbotEmbedding::where('id', $id)->where('user_id', auth()->user()->id)->firstOrFail();
        
        $validation = ['title' => 'required|string|max:255'];
        
        if ($embedding->type === 'text') {
            $validation['content'] = 'required';
        } elseif ($embedding->type === 'qa') {
            $validation['question'] = 'required|string';
            $validation['answer'] = 'required|string';
        }
        
        $request->validate($validation);
        
        if ($embedding->type === 'text' || $embedding->type === 'qa') {
            $content = $request->content;
            if ($embedding->type === 'qa') {
                $content = json_encode([
                    'question' => $request->question,
                    'answer' => $request->answer
                ]);
            }
            
            $embedding->update([
                'title' => $request->title,
                'content' => $content,
                'status' => 'processing'
            ]);
            
            \App\Jobs\GenerateEmbeddingJob::dispatch($embedding);
        } else {
            $embedding->update(['title' => $request->title]);
        }
        
        return redirect()->route('user.extension.chatbot.knowledge')->with('success', 'Knowledge base updated successfully');
    }



    public function manage($id)
    {
        $embedding = ChatbotEmbedding::where('id', $id)->where('user_id', auth()->user()->id)->firstOrFail();
        $chatbots = Chatbot::where('user_id', auth()->user()->id)->get();
        $attachedChatbots = ChatbotKnowledgebase::where('embedding_id', $id)->pluck('chatbot_id')->toArray();
        
        return view('user.external_chatbot.knowledge.manage', compact('embedding', 'chatbots', 'attachedChatbots'));
    }

    public function updateAttachments(Request $request, $id)
    {
        $embedding = ChatbotEmbedding::where('id', $id)->where('user_id', auth()->user()->id)->firstOrFail();
        
        $request->validate([
            'chatbot_ids' => 'required|array|min:1',
            'chatbot_ids.*' => 'exists:chatbots,id'
        ]);

        DB::beginTransaction();
        try {
            ChatbotKnowledgebase::where('embedding_id', $id)->delete();
            
            foreach ($request->chatbot_ids as $chatbotId) {
                ChatbotKnowledgebase::create([
                    'chatbot_id' => $chatbotId,
                    'embedding_id' => $id
                ]);
            }

            DB::commit();
            return redirect()->route('user.extension.chatbot.knowledge')->with('success', 'Chatbot attachments updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update attachments: ' . $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        if ($request->ajax()) {

            $result = ChatbotEmbedding::where('id', request('id'))->firstOrFail();  

            if ($result->user_id == auth()->user()->id){
                ChatbotKnowledgebase::where('embedding_id', request('id'))->delete();

                $result->delete();

                return response()->json('success');    
    
            } else{
                return response()->json('error');
            } 
        }              
    }

}
