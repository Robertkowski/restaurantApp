<?php

namespace App\Controller;

use Exception;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use function is_null;

class ApiController extends AbstractController
{

    const DEFAULT_CACHE_TTL = 300; // seconds

    protected function transformErrors(ConstraintViolationList $errors): array
    {
        $result = [];
        foreach ($errors as $error) {
            /* @var $error ConstraintViolation */
            $result[] = [
                'property' => $error->getPropertyPath(),
                'message' => $error->getMessage()
            ];
        }

        return $result;
    }

    protected function jmsSerialization($serializationObject, $groups = ['Default'], $type = 'json', $serializeNull = false)
    {
        /** @var $serializer Serializer */
        $serializer = $this->get('serializer');
        $serializerContext = SerializationContext::create()->setGroups($groups);

        if ($serializeNull) {
            $serializerContext->setSerializeNull(true);
        }

        return $serializer->serialize($serializationObject, $type);
    }

    protected function jmsDeserialization($content, $class, $groups = ['Default'], $type = 'json')
    {
        /** @var $serializer Serializer */
        $serializer = $this->get('serializer');
        $serializerContext = DeserializationContext::create()->setGroups($groups);

        return $serializer->deserialize($content, $class, $type);
    }

    /**
     * @param string $class
     * @param string[] $groups
     * @param null $requestData
     * @return mixed
     * @throws Exception
     */
    protected function deserializeRequestData(string $class, $groups = ['default'], $requestData = null)
    {
        if (is_null($requestData)) {
            $requestData = $this->container->get('request_stack')->getCurrentRequest()->getContent();
        }
        return $this->jmsDeserialization(
            $requestData,
            $class,
            $groups
        );
    }

}
