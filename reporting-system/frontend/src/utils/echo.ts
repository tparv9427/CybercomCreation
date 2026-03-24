import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

(window as any).Pusher = Pusher

let echo: any = null

export const initEcho = (token: string) => {
  if (echo) return echo

  echo = new Echo({
    broadcaster: 'reverb',
    key: 'reporting-key', // match REVERB_APP_KEY in .env
    wsHost: window.location.hostname,
    wsPort: 8080,
    wssPort: 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/api/broadcasting/auth',
    auth: {
      headers: {
        Authorization: `Bearer ${token}`
      }
    }
  })

  return echo
}

export const getEcho = () => echo
