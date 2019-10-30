This example shows how to log changes, creations and deletions of Basket entity and it's related BasketItem entities to another related BasketTrack entities.

0. Copy the `Service` and `EventListener` folders to your project. EventListener is the entrypoint. Add to your service.yaml:
```
services:

    # ...
    
    App\EventListener\EntityTrackListener:
        tags:
            - { name: doctrine.event_listener, event: onFlush }
```
1. Create `config/app/entity_track.yaml` and import the folder by `imports` at your `services.yaml`:
```

# ...

imports:
    - { resource: 'app/' }
```
2. Create your `Basket` and `BasketItem` entities which implement `TrackableInterface`.
3. Create `BasketTrack` entity which implements `TrackInterface`.
4. Edit `BasketTrackHandler`, `BasketItemTrackHandler` and `TrackHandlers` as you need.
5. Use `getBasketTrackTitle` method of `BasketTrackHandler` to get track title.
