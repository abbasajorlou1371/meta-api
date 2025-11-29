<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTicketRequest;
use App\Http\Requests\TicketResponseRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Notifications\TicketRecieved;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Ticket::class);
    }

    /**
     * Display a listing of the tickets.
     *
     * Retrieves a paginated collection of tickets for the authenticated user.
     * Includes related sender and receiver profile photos and orders by the most recently updated.
     * Filters tickets where the user is the receiver if 'recieved' parameter is present, otherwise filters where the user is the sender.
     *
     * @return App\Http\Resources\TicketResource
     */
    public function index(): AnonymousResourceCollection
    {
        $tickets = Ticket::with('sender.latestProfilePhoto', 'reciever.latestProfilePhoto')
            ->when(request()->boolean('recieved') === true, function ($query) {
                $query->whereBelongsTo(request()->user(), 'reciever');
            }, function ($query) {
                $query->whereBelongsTo(request()->user(), 'sender');
            })
            ->orderByDesc('updated_at')
            ->simplePaginate(10);

        return TicketResource::collection($tickets);
    }

    /**
     * Display the specified ticket.
     *
     * @param Ticket $ticket
     * @return TicketResource
     */
    public function show(Ticket $ticket): TicketResource
    {
        $ticket->load('sender.latestProfilePhoto', 'reciever.latestProfilePhoto', 'responses');

        return new TicketResource($ticket);
    }

    /**
     * Store a newly created ticket in storage.
     *
     * @param CreateTicketRequest $request The request object containing the ticket details.
     * @return TicketResource The resource representation of the created ticket.
     */
    public function store(CreateTicketRequest $request)
    {
        $attachment = '';
        if ($request->hasFile('attachment')) {
            $storedPath = $request->file('attachment')->store('tickets', 'public');
            
            // Remove execution permissions from uploaded file (security measure)
            $fullPath = storage_path('app/public/' . $storedPath);
            if (file_exists($fullPath)) {
                chmod($fullPath, 0644);
            }
            
            $attachment = url('uploads/' . $storedPath);
        }

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'content' => $request->content,
            'attachment' => $attachment,
            'reciever_id' => $request->reciever,
            'department' => $request->department,
            'code' => random_int(100000, 999999),
        ]);

        if ($request->has('reciever')) {
            $ticket->reciever->notify(new TicketRecieved($ticket));
        }

        return new TicketResource($ticket);
    }


    /**
     * Updates an existing ticket with the provided data.
     *
     * @param CreateTicketRequest $request The request object containing the ticket data.
     * @param Ticket $ticket The ticket instance to be updated.
     * @return TicketResource The updated ticket resource.
     */
    public function update(CreateTicketRequest $request, Ticket $ticket)
    {
        $attachment = $ticket->attachment; // Keep existing attachment if no new one uploaded
        
        if ($request->hasFile('attachment')) {
            $storedPath = $request->file('attachment')->store('tickets');
            
            // Remove execution permissions from uploaded file (security measure)
            $fullPath = storage_path('app/' . $storedPath);
            if (file_exists($fullPath)) {
                chmod($fullPath, 0644);
            }
            
            $attachment = url('uploads/' . $storedPath);
        }

        $ticket->update([
            'title' => $request->title,
            'content' => $request->content,
            'attachment' => $attachment,
            'status' => Ticket::NEW,
        ]);

        return new TicketResource($ticket->refresh());
    }

    /**
     * Add a response to the specified ticket.
     *
     * @param TicketResponseRequest $request The request object containing the response details.
     * @param Ticket $ticket The ticket instance to which the response is being added.
     * @return TicketResource|JsonResponse The updated ticket resource or a JSON response.
     */
    public function response(TicketResponseRequest $request, Ticket $ticket)
    {
        $this->authorize('respond', $ticket);
        
        $attachment = '';
        if ($request->hasFile('attachment')) {
            $storedPath = $request->file('attachment')->store('tickets');
            
            // Remove execution permissions from uploaded file (security measure)
            $fullPath = storage_path('app/' . $storedPath);
            if (file_exists($fullPath)) {
                chmod($fullPath, 0644);
            }
            
            $attachment = url('uploads/' . $storedPath);
        }

        TicketResponse::create([
            'ticket_id' => $ticket->id,
            'response' => $request->response,
            'attachment' => $attachment,
            'responser_name' => $request->user()->name,
            'responser_id' => $request->user()->id,
        ]);

        $ticket->update(['status' => Ticket::ANSWERED]);

        $ticket->sender->notify(new TicketRecieved($ticket));

        return new TicketResource($ticket->refresh());
    }

    /**
     * Close the specified ticket.
     *
     * @param Ticket $ticket The ticket instance to be closed.
     * @return JsonResponse The response indicating the ticket has been closed.
     */
    public function close(Ticket $ticket)
    {
        $this->authorize('close', $ticket);

        $ticket->update(['status' => Ticket::CLOSED]);

        return new TicketResource($ticket->refresh());
    }
}
