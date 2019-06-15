<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2019 Bielov Andrii
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @category Stagem
 * @package Stagem_<package>
 * @author Bielov Andrii <bielovandrii@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Popov\ZfcUser\GraphQL\Mutation;

use GraphQL\Doctrine\Types;
use GraphQL\Type\Definition\Type;
use Zend\Stdlib\Exception\InvalidArgumentException;

class LoginMutation
{
    public function __invoke(Types $types)
    {
        return [
            'login' => [
                //'type' => Type::nonNull($this->types->getOutput(User::class)),
                'type' => new \GraphQL\Type\Definition\ObjectType([
                    'name' => 'Token',
                    'fields' => [
                        'token' => Type::nonNull(Type::string()),
                    ],
                ]),
                'args' => [
                    'email' => Type::nonNull(Type::string()), // Use standard API when needed
                    'password' => Type::nonNull(Type::string()), // Use standard API when needed
                    //'input' => $this->types->getPartialInput(Post::class),  // Use automated InputObjectType for partial input for updates
                ],
                'resolve' => function ($root, $args, $context) {
                    if ($user = $context->user) {
                        return ['token' => session_id()];
                    }
                    throw new InvalidArgumentException(
                        'GraphQLMiddleware cannot find user with credential passed to LoginMutation'
                    );
                },
            ],
        ];
    }
}