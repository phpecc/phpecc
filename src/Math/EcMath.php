<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Primitives\PointInterface;

class EcMath implements EcMathInterface
{
    /**
     * @var int|PointInterface
     */
    private $data;

    /**
     * @var string
     */
    private $dataType;

    /**
     * @var MathAdapterInterface
     */
    private $math;

    /**
     * @var GeneratorPoint
     */
    private $generator;

    /**
     * @param $input
     * @param GeneratorPoint       $G
     * @param MathAdapterInterface $math
     */
    public function __construct($input, GeneratorPoint $G, MathAdapterInterface $math)
    {
        $this->dataType  = $this->identify($input);
        $this->data      = $input;
        $this->math      = $math;
        $this->generator = $G;
        $this->modMath = $math->getModularArithmetic($G->getOrder());
    }

    /**
     * @param $input
     * @return string
     * @throws \LogicException
     */
    public function identify($input)
    {
        if ($input instanceof PointInterface) {
            return 'point';
        } elseif (is_numeric($input)) {
            return 'int';
        }

        throw new \LogicException('Must provide a point or integer');
    }

    /**
     * This function handles cases where the two types are not identical (but
     * the result will always be a point).
     *
     * Since the current data could be either a point or an int, (with the operand taking
     * the opposite type), this function ensures that the callable $handler ALWAYS receives
     * it's arguments in the order of (Point, int).
     *
     * @param  int|PointInterface $operand
     * @param  callable           $handler
     * @return $this
     */
    private function handleOppositeTypes($operand, callable $handler)
    {
        $data =  $this->data;
        if (false === $data instanceof PointInterface) {
            list($data, $operand) = array($operand, $data);
        }

        $this->dataType = 'point';

        // handler (currentData, operand):
        //   <operation> (point, int) -> point
        $this->data = $handler($data, $operand);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\EcMathInterface::add()
     */
    public function add($addend)
    {
        $type = $this->identify($addend);

        if ($this->dataType == 'point' && $type == 'point') {
            $this->data = $this->data->add($addend);
            return $this;
        }

        if ($this->dataType == 'int' && $type == 'int') {
            $this->data = $this->modMath->add($this->data, $addend);
            return $this;
        }

        $this->handleOppositeTypes(
            $addend,
            function (PointInterface $data, $addendInt) {
                // Multiply by generator and return a regular point to add to $data
                $point = $this->generator->mul($addendInt);
                //$point = $this->generator->getCurve()->getPoint($point->getX(), $point->getY(), $this->generator->getOrder());

                return $data->add($point);
            }
        );

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\EcMathInterface::mul()
     */
    public function mul($multiplicand)
    {
        $type = $this->identify($multiplicand);

        if ($this->dataType == 'point' && $type == 'point') {
            throw new \LogicException('Cannot multiply two points together');
        }

        if ($this->dataType == 'int' && $type == 'int') {
            $this->data = $this->modMath->mul($this->data, $multiplicand);
            return $this;
        }

        $this->handleOppositeTypes(
            $multiplicand,
            function (PointInterface $data, $multiplicandInt) {
                return $data->mul($multiplicandInt);
            }
        );

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\EcMathInterface::getDouble()
     */
    public function getDouble()
    {
        $data = $this->data;

        if ($this->dataType == 'point') {
            $this->data = $data->getDouble();
            return $this;
        } else {
            $this->data = $this->modMath->mul($data, '2');

            return $this;
        }
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\EcMathInterface::mod()
     */
    public function mod($n)
    {
        if ($this->identify($n) !== 'int') {
            throw new \LogicException('Parameter for mod() must be an integer');
        }

        if ($this->dataType == 'int') {
            $this->data = $this->math->mod($this->data, $n);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\EcMathInterface::cmp()
     */
    public function cmp($input)
    {
        $type = $this->identify($input);

        if ($this->dataType == 'point' && $type == 'point') {
            return $this->data->cmp($input);
        }

        if ($this->dataType == 'int' && $type == 'int') {
            return $this->math->cmp($this->data, $input);
        }

        throw new \LogicException('Cannot compare values of different types');
    }

    /**
     * @return $this
     */
    public function toPoint()
    {
        if ($this->dataType == 'point') {
            return $this;
        }

        $this->mul($this->generator);
        $this->dataType = 'point';

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\EcMathInterface::result()
     */
    public function result()
    {
        $data = $this->data;

        return $data;
    }

    /**
     * Return the point associated with the value in the instance.
     *
     * @return int|PointInterface
     */
    public function getPoint()
    {
        if ($this->dataType == 'point') {
            $point = $this->data;
            return $point;
        } else {
            $self = new EcMath($this->data, $this->generator, $this->math);
            $self->mul($this->generator);
            $point = $self->result();
            return $point;
        }
    }

    /**
     * Return the type of the value in the instance.
     *
     * @return string
     */
    public function getType()
    {
        return $this->dataType;
    }
}
