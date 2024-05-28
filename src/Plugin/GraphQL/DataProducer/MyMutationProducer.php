<?php

declare(strict_types=1);

namespace Drupal\my_mutant\Plugin\GraphQL\DataProducer;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\GraphQL\Execution\FieldContext;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This is called by the SchemaExtension to do the actual work.
 *
 * @DataProducer(
 *   id = "my_mutation_producer",
 *   name = @Translation("Mutation"),
 *   description = @Translation("Mutation extension."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("NodeInterface"),
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Mutation data"),
 *     ),
 *   },
 * )
 */
class MyMutationProducer extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected AccountInterface $currentUser;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
    );

    $instance->currentUser = $container->get('current_user');
    $instance->entityTypeManager = $container->get('entity_type.manager');

    return $instance;
  }

  /**
   * An example mutation.
   *
   * @param array $data
   *   The mutation data.
   * @param \Drupal\graphql\GraphQL\Execution\FieldContext $context
   *   The cache context.
   *
   * @return \Drupal\node\NodeInterface|null
   *   The created comment.
   *
   * @throws \GraphQL\Error\UserError
   *   Validation error.
   */
  public function resolve(array $data, FieldContext $context): ?NodeInterface {
    $node = $this->entityTypeManager
      ->getStorage('node')
      ->load($data['id']);

    if (!$node instanceof NodeInterface) {
      return NULL;
    }

    $access = $node->access('update', $this->currentUser, TRUE);
    $context->addCacheableDependency($access);

    if (!$access->isAllowed()) {
      return NULL;
    }

    $node->set('title', $data['title']);
    $node->save();

    $context->addCacheableDependency($node);

    return $node;
  }

}
