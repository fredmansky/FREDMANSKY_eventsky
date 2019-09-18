<?php


namespace fredmansky\eventsky;

use Craft;
use craft\web\Controller;
use fredmansky\eventsky\elements\Ticket;

/**
 * The TicketsController class is a controller that handles various ticket related tasks such as retrieving, saving,
 * swapping between ticket types, and deleting tickets.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0
 */
class TicketsController extends Controller
{
  // Public Methods
  // =========================================================================

  /**
   * Saves a ticket.
   */
  public function actionSaveTicket()
  {
    // Create a new ticket element
    $ticket = new Ticket();

    // Set the main properties from POST data
    $ticket->description = Craft::$app->request->getBodyParam('description');
    $ticket->authorId = Craft::$app->request->getBodyParam('authorId');
    $ticket->ticketTypeId = Craft::$app->request->getBodyParam('ticketTypeId');

    // Save the ticket
    $success = Craft::$app->elements->saveElement($ticket);
  }


  /**
   * Deletes a ticket.
   */
  public function actionDeleteEvent()
  {
    $this->requirePostRequest();

    $ticketId = craft()->request->getRequiredPost('ticketId');

    if (craft()->elements->deleteElementById($ticketId))
    {
      craft()->userSession->setNotice(Craft::t('Ticket deleted.'));
      $this->redirectToPostedUrl();
    }
    else
    {
      craft()->userSession->setError(Craft::t('Couldn’t delete ticket.'));
    }
  }
}
