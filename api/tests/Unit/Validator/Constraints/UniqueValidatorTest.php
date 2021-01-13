<?php

namespace App\Tests\Unit\Validator\Constraints;

use App\Validator\Constraints\Unique;
use App\Validator\Constraints\UniqueValidator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

class UniqueValidatorTest extends TestCase
{
    const MESSAGE = 'validation_message';
    const CLASS_NAME = 'className';
    const FIELD_NAME = 'fieldName';
    const TRANSLATED = 'translated';

    /**
     * @var MockObject |TranslatorInterface
     */
    private MockObject $translator;

    private Unique $constraint;

    public function setUp(): void
    {
        $this->translator = $this->mockTranslator();
        $this->constraint = $this->getConstraint();
    }

    public function getContext($expectedMessage = null): void
    {
        $builder = $this->createMock(ConstraintViolationBuilder::class);
        $context = $this->createMock(ExecutionContext::class);

        if ($expectedMessage) {
            $builder->expects($this->once())->method('addViolation');

            $context->expects($this->once())
                ->method('buildViolation')
                ->with($this->equalTo(self::TRANSLATED))
                ->will($this->returnValue($builder));
        } else {
            $context->expects($this->never())->method('buildViolation');
        }

        return $context;
    }

    public function testValidateOnInvalid(): void
    {
        $repository = $this->createMock(ServiceEntityRepository::class);
        $repository->method('findOneBy')->willReturn('something');
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturn($repository);

        $uniqueValidator = new UniqueValidator($em, $this->translator);
        $uniqueValidator->initialize($this->getContext($this->constraint->getMessage()));
        $uniqueValidator->validate('value', $this->constraint);
    }

    public function testValidateOnValid(): void
    {
        $repository = $this->createMock(ServiceEntityRepository::class);
        $repository->method('findOneBy')->willReturn(null);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturn($repository);

        $uniqueValidator = new UniqueValidator($em, $this->translator);
        $uniqueValidator->initialize($this->getContext());
        $uniqueValidator->validate('invalidValue', $this->constraint);
    }

    /**
     * @return MockObject|TranslatorInterface
     */
    private function mockTranslator(): MockObject
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturn('translated');

        return $translator;
    }

    private function getConstraint(): Unique
    {
        return new Unique([
            'message' => self::MESSAGE,
            'class' => self::CLASS_NAME,
            'field' => self::FIELD_NAME,
        ]);
    }
}
