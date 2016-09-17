<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends MY_Controller
{
	function __construct()
	{
		parent::__construct();

      $this->load->helper(array('form', 'url'));
    }

	function vote($meteor_id = NULL, $vote_id = NULL)
	{
		$this->load->model(array('votes_model', 'votes_tracker_model'));

        $link_id = 'vote_'.$meteor_id.'_'.$vote_id;

        if (empty($vote_id))
        {
            $vote = $this->votes_model->find(array(
                'meteor_id' => $meteor_id
            ));

            if (empty($vote))
            {
                $vote_id = $this->votes_model->save(array(
                    'meteor_id' => $meteor_id
                    , 'votes_cnt' => 0
                ));
            }
            else
            {
                $vote_id = $vote->vote_id;
            }
        }

        $vote = $this->votes_model->find(array(
            'vote_id' => $vote_id
            ,'meteor_id' => $meteor_id
        ));


		if ($vote && !$this->votes_tracker_model->findCount(array(
            'vote_id' => $vote_id
            ,'ip_address' => sprintf("%u", ip2long($this->input->ip_address()))
            ,'voted_at >=' => time() - 60*60*24
        )))
		{
            $this->db->set('votes_cnt', 'votes_cnt+1', FALSE)->where('vote_id', $vote_id)->update($this->votes_model->table);
            $this->votes_tracker_model->save(array(
                'vote_id' => $vote_id
                ,'ip_address' => sprintf("%u", ip2long($this->input->ip_address()))
                ,'voted_at' => time()
            ));

            //$this->taconite->set('eval', '$("a.'.$link_id.'").html(\'' .  '<i class="icon-thumbs-up">&nbsp;</i> '.($vote->votes_cnt+1) . '\');');
            echo ($vote->votes_cnt + 1);
		}
        else
        {
           //$this->taconite->set('eval', 'alert("You already voted for this one! Pick another favourite meteor please.")');
           echo 'You already voted for this one! Pick another favourite meteor please.';
        }
	}

}
