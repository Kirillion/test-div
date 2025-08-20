<?php

namespace tests\unit\models;

use app\models\Request;
use Yii;
use yii\mail\MailerInterface;
use yii\mail\MessageInterface;
use Codeception\Test\Unit;

class RequestTest extends Unit
{
    public function testEmailTriggeredOnStatusChange()
    {
        $mailerMock = $this->createMock(MailerInterface::class);
        $messageMock = $this->createMock(MessageInterface::class);

        $mailerMock->expects($this->once())
            ->method('compose')
            ->with('request/answer', $this->anything())
            ->willReturn($messageMock);

        $messageMock->expects($this->once())->method('setFrom')->with(Yii::$app->params['senderEmail'])->willReturnSelf();
        $messageMock->expects($this->once())->method('setTo')->with('user@example.com')->willReturnSelf();
        $messageMock->expects($this->once())->method('setSubject')->with($this->stringContains('Ответ на запрос:'))->willReturnSelf();
        $messageMock->expects($this->once())->method('send')->willReturn(true);

        Yii::$app->set('mailer', $mailerMock);

        $request = new Request([
            'name' => 'TestUser',
            'email' => 'user@example.com',
            'comment' => 'Test comment',
            'message' => 'Test message',
            'status' => Request::STATUS_ACTIVE,
        ]);

        $request->save();

        $request->status = Request::STATUS_RESOLVED;
        $request->save();

        $this->assertEquals(Request::STATUS_RESOLVED, $request->status);
    }

    public function testEmailTemplateRendering()
    {
        $request = new Request([
            'id' => 1,
            'name' => 'TestUser',
            'comment' => 'Test comment',
        ]);

        $content = Yii::$app->view->render('@app/mail/request/answer.php', ['request' => $request]);

        $this->assertStringContainsString('Добрый день, TestUser', $content);
        $this->assertStringContainsString('Ваш запрос № 1', $content);
        $this->assertStringContainsString('Test comment', $content);
    }

    public function testEmailNotSentOnFailure()
    {
        $request = new Request([
            'id' => 2,
            'name' => 'FailUser',
            'email' => 'fail@example.com',
            'comment' => 'Fail comment',
            'status' => Request::STATUS_ACTIVE,
        ]);

        $mailerMock = $this->createMock(MailerInterface::class);
        $messageMock = $this->createMock(MessageInterface::class);

        $mailerMock->method('compose')->willReturn($messageMock);
        $messageMock->method('setFrom')->willReturnSelf();
        $messageMock->method('setTo')->willReturnSelf();
        $messageMock->method('setSubject')->willReturnSelf();
        $messageMock->method('send')->willReturn(false);

        Yii::$app->set('mailer', $mailerMock);

        $request->status = Request::STATUS_RESOLVED;
        $request->save();

        $this->assertEquals(Request::STATUS_RESOLVED, $request->status);
    }
}
