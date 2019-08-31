This example shows how to log changes, creations and deletions of Basket entity and it's related BasketItem entites to another related BasketTrack entites.

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
3. Create `BasketTrack` entity which implements `TrackInterface` (`BasketTrack` - 'the name of tracking entity' with 'Track' suffix). 
4. Edit `BasketTrackHandler` and `BasketItemTrackHandler` as you need. If you persist any entity, use `computeChangeSet` of the `UnitOfWork`.
5. Use `getBasketTrackTitle` method of `BasketTrackHandler` to get track title.
