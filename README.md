## Sample GraphQL mutation

GraphQL Compose does not need to do any special integration for mutations.

You can leverage the GraphQL Compose types if required.

This example is a simple mutation that updates a node title.

- Save module as `my_mutant` in your `web/modules/custom` directory.
- Enable the module

```graphql
mutation {
  myMutation(data: { id: "1", title: "Yoyo bonkers" }) {
    ... on NodeInterface {
      id
      title
    }
  }
}
```
