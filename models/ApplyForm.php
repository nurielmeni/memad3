<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class ApplyForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $cvFile;
    public $jobId;
    public $jobTitle;
    private $cvFileUrl;

    public function rules()
    {
        return [
            [['cvFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'docx, doc, pdf'],
            [['jobId', 'jobTitle'], 'safe'],
        ];
    }
    
    public function upload()
    {
        if ($this->validate()) {
            $this->cvFileUrl = 'uploads/cvFiles/' . $this->cvFile->baseName . '.' . $this->cvFile->extension;
            $this->cvFile->saveAs($this->cvFileUrl);
            return true;
        } else {
            return false;
        }
    }
    
    public function sendMail() {
        $subject = "התקבלה מועמדות למשרה - $this->jobTitle - מזהה משרה - $this->jobId";
        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setSubject($subject)
                ->setTextBody($subject)
                ->attach($this->cvFileUrl)
                ->send();

            return true;
        }
        return false;
    }
}