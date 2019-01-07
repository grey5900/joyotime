<?php
class AutoReplyHistoryFixture extends CakeTestFixture {
	public $import = array('model' => 'AutoReplyHistory');

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'user_id' => 1,
                'method' => 'receive',
                'message_type' => 'text',
                'client_user' => 'oAIbqjrHMCQM5usC9yFm_IBfePOI',
                'raw' => '<xml><ToUserName><![CDATA[gh_251a88144711]]></ToUserName>
<FromUserName><![CDATA[oAIbqjrHMCQM5usC9yFm_IBfePOI]]></FromUserName>
<CreateTime>1371549869</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[Yyhh]]></Content>
<MsgId>5890761832288084909</MsgId>
</xml>',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
            ),
        );
        parent::init();
    }
}