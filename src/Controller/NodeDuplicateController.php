<?php
// NOTE:
// We intentionally do NOT control redirect behavior here.
// The admin UI (e.g. /admin/content) injects a `destination`
// parameter, which returns the user to the listing immediately.
//
// If this controller is accessed outside that context,
// behavior may differ (redirect may occur).
declare(strict_types=1);

namespace Drupal\node_duplicate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class NodeDuplicateController extends ControllerBase {

  public function duplicate(NodeInterface $node): RedirectResponse {
    $account = $this->currentUser();

    // Access check.
    $create_access = $this->entityTypeManager()
                          ->getAccessControlHandler('node')
                          ->createAccess($node->bundle(), $account, [], TRUE);

    if (!$create_access->isAllowed() || !$node->access('view', $account)) {
      throw $this->createAccessDeniedException();
    }

    // Duplicate.
    $duplicate = $node->createDuplicate();

    $original_title = $node->label() ?? 'Untitled';
    $duplicate->setTitle($original_title . ' (Copy)');

    // Set current user as author if possible.
    if ($duplicate->hasField('uid')) {
      $duplicate->setOwnerId((int) $account->id());
    }

    $time = \Drupal::time()->getCurrentTime();

    if (method_exists($duplicate, 'setCreatedTime')) {
      $duplicate->setCreatedTime($time);
    }

    if (method_exists($duplicate, 'setChangedTime')) {
      $duplicate->setChangedTime($time);
    }

    if (method_exists($duplicate, 'setUnpublished')) {
      $duplicate->setUnpublished();
    }

    // Do NOT mess with revisions unless you really need to.

    $duplicate->save();

    // Message.
    $this->messenger()->addStatus($this->t('Created duplicate: %title', [
      '%title' => $duplicate->label(),
    ]));

    // Hard redirect (no Drupal render involvement).
    $url = Url::fromRoute('entity.node.edit_form', [
      'node' => $duplicate->id(),
    ])->toString();

    return new RedirectResponse($url);
  }

}
