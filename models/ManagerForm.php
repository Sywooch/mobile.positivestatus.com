<?php

namespace app\models;

use app\components\Y;
use Yii;
use yii\base\Model;

use kartik\mpdf\Pdf;

/**
 * ContactForm is the model behind the contact form.
 */
class ManagerForm extends Model
{
//    public $trans_id;
//    public $user_id;
    public $pay_id;
//    public $contact_name;
//    public $message;
//    public $contact_type;       // Phone, Email, Skype ... etc
//    public $contact_value;      // 123-123, qwer@gmail.com ... etc
//    public $verify_code;

    public function rules() {
//        $rules = [
//            [['trans_id', 'user_id', 'contact_name', 'message', 'contact_type', 'contact_value'], 'required'],
//            [['trans_id', 'user_id'], 'integer'],
//        ];
            $rules = [
                [['pay_id'], 'required'],
                [['pay_id'], 'integer'],
            ];

//        if (Y::showCaptcha())
//            $rules[] = ['verify_code', 'captcha'];

        return $rules;
    }

    public function attributeLabels()  {
        return [
//            'pay_id' => Yii::t('site', 'PAY_ID'),
            'pay_id' => 'Pay id',

        ];
    }
    public function generatePdfContent($id){
//        $pay_date = date("d-m-Y");
//        $expire_date = date("d-m-Y", strtotime("+1 months"));
        $pay = Pay::findOne($id);
        $user = User::findOne($pay->user_id);
        $profile = UserProfile::findOne(['user_id' => $pay->user_id]);
        $contacts = UserContact::find()->where(['user_id' => $pay->user_id])->indexBy('id')->all();
        $pay_date = $pay->getDate1();
        $expire_date = $pay->getDate2();

        $html = '';
        $html .= '<!DOCTYPE html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <title>Example 1</title>
                <link rel="stylesheet" href="style.css" media="all" />
              </head>
              <body>
                <header class="clearfix">
                  <h1>INVOICE 321</h1>
                  <div id="company" class="clearfix">
                    <div>Company Name</div>
                    <div>455 Foggy Heights,<br /> AZ 85004, US</div>
                    <div>(602) 519-0450</div>
                    <div><a href="mailto:company@example.com">company@example.com</a></div>
                  </div>
                  <div id="project">
                    <div><span>CLIENT</span> '.$user->name.'</div>
                    <div><span>ADDRESS</span> '.$profile->zip .' '. $profile->address.'</div>
                    <div><span>EMAIL</span> <a href="mailto:'.$user->email.'">'.$user->email.'</a></div>
                    <div><span>DATE </span>' .$pay_date.'</div>
                    <div><span>DUE DATE </span> '.$expire_date.'</div>
                  </div>
                </header>
                <main>
                  <table>
                    <thead>
                      <tr>
                        <th class="desc">DESCRIPTION</th>
                        <th>PRICE</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="desc">Firm name:'.$user->name.' from '. $pay_date.' to '.$expire_date.'  Client Number:'.Y::getPaymentId($id).'</td>
                        <td class="unit">€50.00</td>
                      </tr>
                      <tr>
                        <td class="grand total">TOTAL</td>
                        <td class="grand total">€50</td>
                      </tr>
                    </tbody>
                  </table>

                </main>
                <footer>
                  Invoice was created on a computer and is valid without the signature and seal.
                </footer>
              </body>
            </html>';

        return $html;
    }
    public function getPdfCss(){
        $css = null;
        $css .= '
             .clearfix:after {
                  content: "";
                  display: table;
                  clear: both;
                }
                
                a {
                  color: #5D6975;
                  text-decoration: underline;
                }
                
                body {
                  position: relative;
                  width: 21cm;  
                  height: 29.7cm; 
                  margin: 0 auto; 
                  color: #001028;
                  background: #FFFFFF; 
                  font-family: Arial, sans-serif; 
                  font-size: 12px; 
                  font-family: Arial;
                }
                
                header {
                  padding: 10px 0;
                  margin-bottom: 30px;
                }
                
                #logo {
                  text-align: center;
                  margin-bottom: 10px;
                }
                
                #logo img {
                  width: 90px;
                }
                
                h1 {
                  border-top: 1px solid  #5D6975;
                  border-bottom: 1px solid  #5D6975;
                  color: #5D6975;
                  font-size: 2.4em;
                  line-height: 1.4em;
                  font-weight: normal;
                  text-align: center;
                  margin: 0 0 20px 0;
                  background: url(dimension.png);
                }
                
                #project {
                  float: left;
                }
                
                #project span {
                  color: #5D6975;
                  text-align: right;
                  width: 52px;
                  margin-right: 10px;
                  display: inline-block;
                  font-size: 0.8em;
                }
                
                #company {
                  float: right;
                  text-align: right;
                }
                
                #project div,
                #company div {
                  white-space: nowrap;        
                }
                
                table {
                  width: 100%;
                  border-collapse: collapse;
                  border-spacing: 0;
                  margin-bottom: 20px;
                }
                
                table tr:nth-child(2n-1) td {
                  background: #F5F5F5;
                }
                
                table th,
                table td {
                  text-align: center;
                }
                
                table th {
                  padding: 5px 20px;
                  color: #5D6975;
                  border-bottom: 1px solid #C1CED9;
                  white-space: nowrap;        
                  font-weight: normal;
                }
                
                table .service,
                table .desc {
                  text-align: left;
                }
                
                table td {
                  padding: 20px;
                  text-align: right;
                }
                
                table td.service,
                table td.desc {
                  vertical-align: top;
                }
                
                table td.unit,
                table td.qty,
                table td.total {
                  font-size: 1.2em;
                }
                
                table td.grand {
                  border-top: 1px solid #5D6975;;
                }
                
                #notices .notice {
                  color: #5D6975;
                  font-size: 1.2em;
                }
                
                footer {
                  color: #5D6975;
                  width: 100%;
                  height: 30px;
                  position: absolute;
                  bottom: 0;
                  border-top: 1px solid #C1CED9;
                  padding: 8px 0;
                  text-align: center;
               }\'
        ';
        return $css;
    }

    public function generatePdf($id){
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            //html content input
            'content' => $this->generatePdfContent($id),
            'cssInline' => $this->getPdfCss(),
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader'=>['Krajee Report Header'],
                'SetFooter'=>['{PAGENO}'],
            ]
        ]);
        return $pdf->render();
    }


    ////////////////////////////////////////////////////////////
    public function sendMessage() {
        $pay = Pay::findOne($this->pay_id);
        $user = User::findOne($pay->user_id);

        $pdf = $this->generatePdf($this->pay_id);
        $res = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($user->email)
            // ->setTo('brumak7@gmail.com')
            ->setSubject('Оплатите аккаунт')
            ->setHtmlBody('Счет во вложении')
            ->attachContent($pdf, ['fileName' => 'invoice.pdf', 'contentType' => 'application/pdf'])
            ->send();
        return $res;

    }

    public function sendWarning($days_left) {
        $pay = Pay::findOne($this->pay_id);
        $user = User::findOne($pay->user_id);
        $text = 'Здравствуйте!напоминаем вам о необходимости оплаты аккаунта Бизнес срок действия которого заканчивается напоминаем вам что если не поступит оплата то аккаунт Бизнес будет автоматически переведен на Басик и на сайте будут показаны только последние 3 ваших добавленных предложения';
        $pdf = $this->generatePdf($this->pay_id);
        $res = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($user->email)
            // ->setTo('brumak7@gmail.com')
            ->setSubject('Оплатите аккаунт')
            ->setTextBody($text)
            ->attachContent($pdf, ['fileName' => 'invoice.pdf', 'contentType' => 'application/pdf'])
            ->send();
        return $res;

    }

    public function sendNotification(){
        $pay = Pay::findOne($this->pay_id);
        $user = User::findOne($pay->user_id);

        $res = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($user->email)
            // ->setTo('brumak7@gmail.com')
            ->setSubject(Yii::t('user', 'REGISTRATIONMAIL_SUBJECT'))
            ->setHtmlBody(Yii::t('user', 'REGISTRATIONMAIL_BODY'))
            ->send();
        return $res ? ['result' => 'ok', 'message' => Yii::t('site', 'BID_SENT')] : ['result' => 'err', 'message' => Yii::t('site', 'SEND_EMAIL_ERR')];

    }
}
