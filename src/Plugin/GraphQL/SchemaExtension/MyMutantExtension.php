<?php

namespace Drupal\my_mutant\Plugin\GraphQL\SchemaExtension;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql\Plugin\GraphQL\SchemaExtension\SdlSchemaExtensionPluginBase;

/**
 * Example schema ext.
 *
 * @SchemaExtension(
 *   id = "my_mutant",
 *   name = "Example extension",
 *   description = "A simple extension that adds node related fields.",
 *   schema = "graphql_compose"
 * )
 */
class MyMutantExtension extends SdlSchemaExtensionPluginBase {

  /**
   * {@inheritdoc}
   */
  public function registerResolvers(ResolverRegistryInterface $registry) {
    $builder = new ResolverBuilder();

    // This is telling the GraphQL module how to handle the incoming query.
    $registry->addFieldResolver(
      'Mutation',
      'myMutation',
      $builder->produce('my_mutation_producer')
        ->map('data', $builder->fromArgument('data'))
    );
  }

}
